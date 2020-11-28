// @flow 
import { Box, Button, ButtonProps, Checkbox, FormControlLabel, makeStyles, TextField, Theme } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';
import { useState, useEffect } from 'react';
import { yupResolver } from '@hookform/resolvers/yup';
import categoryHttp from '../../util/http/category-http';
import * as yup from '../../util/vendor/yup';
import { useHistory, useParams } from 'react-router';
import { useSnackbar } from 'notistack';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string()
        .label('Nome')
        .required(),
});
export const Form = () => {

    const classes = useStyles();


    //Using component react-hook-form 
    const { register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch } = useForm({
            defaultValues: {
                name: null,
                is_active: true
            },
            resolver: yupResolver(validationSchema),
        });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [category, setCategory] = useState<{ id: string } | null>(null);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: 'secondary',
        variant: 'contained',
        disabled: loading
    }

    useEffect(() => {
        if (!id) {
            return;
        }
        async function getCategory() {
            setLoading(true);
            try {
                const { data } = await categoryHttp.get(id);
                setCategory(data.data);
                reset(data.data);
            } catch (error) {
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    { variant: 'error' }
                );
            }
            finally {
                setLoading(false)
            }
        };
        // Call declared function up
        getCategory();
    }, []);

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

        }
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)} >
            <TextField
                name='name'
                label='Nome'
                fullWidth
                variant='outlined'
                margin='normal'
                inputRef={register}
                disabled={loading}
                error={errors.name !== undefined}
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
            <Box dir={'rtl'} >
                <Button
                    color={'primary'}
                    {...buttonProps}
                    onClick={() => onSubmit(getValues(), null)}
                >
                    Salvar
                    </Button>
                <Button
                    color={'secondary'}
                    {...buttonProps}
                    type='submit'
                >
                    Salvar e continuar editando
                    </Button>
            </Box>
        </form >
    );
};