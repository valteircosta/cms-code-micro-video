// @flow 
import { Checkbox, FormControlLabel, TextField } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';
import { useState, useEffect } from 'react';
import categoryHttp from '../../util/http/category-http';
import * as yup from '../../util/vendor/yup';
import { useHistory, useParams } from 'react-router';
import { useSnackbar } from 'notistack';
import { Category } from '../../util/models';
import SubmitActions from '../../components/SubmitActions';
import { DefaultForm } from '../../components/DefaultForm';


const validationSchema = yup.object().shape({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255),
});

export const Form = () => {

    //Using component react-hook-form 
    const { register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        trigger,
    } = useForm({
        defaultValues: {
            name: null,
            is_active: true
        },
        resolver: yupResolver(validationSchema),
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [category, setCategory] = useState<Category | null>(null);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {
        if (!id) {
            return;
        }
        // We can work with pattern IIFE (Immediately Invokable Function Expressions)
        let isSubscribed = true;
        (async function getCategory() {
            setLoading(true);
            try {
                const { data } = await categoryHttp.get(id);
                if (isSubscribed) {
                    setCategory(data.data);
                    reset(data.data);
                }

            } catch (error) {
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    { variant: 'error' }
                );
            }
            finally {
                setLoading(false)
            }

        })(); // Call with IIFE
        return () => {
            isSubscribed = false;
        }
    }, [id, reset, snackbar]);

    //Used for make bind between components, in case checkbox
    useEffect(() => {
        register({ name: 'is_active' })
    }, [register]);//Look [register] is dependence passed to hook


    async function onSubmit(formData, event) {
        try {
            setLoading(true);
            const http = !category
                ? categoryHttp.create(formData)
                : categoryHttp.update(category.id, formData);
            const { data } = await http;
            snackbar.enqueueSnackbar(
                'Categoria salva com sucesso',
                { variant: 'success' }
            );
            setTimeout(() => {
                // Is event check button clicked
                event
                    ? (
                        id
                            //Has id is editing else add
                            ? history.replace(`/categories/${data.data.id}/edit`)
                            : history.push(`/categories/${data.data.id}/edit`)
                    )
                    : history.push('/categories')
            });
        } catch (error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não foi possível salvar a categoria',
                { variant: 'error' }
            );
        } finally {
            setLoading(false);
        }
    };

    return (

        <DefaultForm
            GridItemProps={{xs: 12, md: 6 }}
            onSubmit={handleSubmit(onSubmit)}
        >
            <TextField
                name='name'
                label='Nome'
                fullWidth
                variant='outlined'
                margin='normal'
                inputRef={register}
                disabled={loading}
                error={errors.name !== undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{ shrink: true }}
            />
            <TextField
                name='description'
                label='Descrição'
                multiline
                rows='4'
                fullWidth
                variant='outlined'
                margin='normal'
                inputRef={register}
                disabled={loading}
                InputLabelProps={{ shrink: true }}
            />
            <FormControlLabel
                control={
                    <Checkbox
                        name='is_active'
                        onChange={
                            () => setValue('is_active', !getValues()['is_active'])
                        }
                        checked={watch('is_active')}
                    />
                }
                label={'Ativo?'}
                labelPlacement={'end'}
                disabled={loading}
            />
            <SubmitActions
                disableButtons={loading}
                handleSave={() =>
                    trigger().then(isValid => {
                        onSubmit(getValues(), null)
                    })
                }
            />
        </DefaultForm >
    );
};