// @flow 

import React, { useEffect, useState } from 'react';
import { MenuItem, TextField } from '@material-ui/core';
import { useForm } from 'react-hook-form';
import genreHttp from '../../util/http/genre-http';
import categoryHttp from '../../util/http/category-http';
import * as yup from '../../util/vendor/yup';
import { useHistory, useParams } from 'react-router';
import { useSnackbar } from 'notistack';
import { Category, Genre } from '../../util/models';
import SubmitActions from '../../components/SubmitActions';
import { DefaultForm } from '../../components/DefaultForm';



const validationSchema = yup.object().shape({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255),
    categories_id: yup.array()
        .label('Categorias')
        .required(),
});
export const Form = () => {

    //Using component react-hook-form
    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        watch,
        errors,
        reset,
        triggerValidation,
    } = useForm({
        validationSchema
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [genre, setGenre] = useState<Genre | null>(null);
    const [categories, setCategories] = useState<Category[]>([]);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {

        let isSubscribed = true;
        (async function loadData() {
            setLoading(true);
            /** Define a Promise array, now can add new resources dependencies */
            const promises = [categoryHttp.list({ queryParams: { all: '' } })];
            if (id) {
                promises.push(genreHttp.get(id));
            }
            try {
                /** Promise.all() resolve all promises in parallel returning results together */
                const [categoriesResponse, genreResponse] = await Promise.all(promises);
                if (isSubscribed) {
                    setCategories(categoriesResponse.data.data);
                    if (id) {
                        setGenre(genreResponse.data.data);
                        const categories_id = genreResponse.data.data.categories.map(category => category.id);
                        reset({
                            ...genreResponse.data.data,
                            categories_id: categories_id
                        });
                    }
                }


            } catch (error) {
                console.error(error);
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    { variant: 'error' }
                );
            }
            finally {
                setLoading(false)
            }

            return () => {
                isSubscribed = false;
            }
        })();
    }, [id, reset, snackbar]);

    //Used for make bind between components
    useEffect(() => {
        register({ name: 'categories_id' })
    }, [register]);//Look [register] is dependence passed to hook

    async function onSubmit(formData, event) {
        try {
            setLoading(true);
            const http = !genre
                ? genreHttp.create(formData)
                : genreHttp.update(id, formData);
            const { data } = await http;
            snackbar.enqueueSnackbar(
                'Gênero salvo com sucesso',
                { variant: 'success' }
            );
            setTimeout(() => {
                // Is event check button clicked
                event
                    ? (
                        id
                            //Has id is editing else add
                            ? history.replace(`/genres/${data.data.id}/edit`)
                            : history.push(`/genres/${data.data.id}/edit`)
                    )
                    : history.push('/genres')
            });
        } catch (error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não foi possível salvar gênero',
                { variant: 'error' }
            );
        } finally {
            setLoading(false);
        }
    };

    return (

        <DefaultForm
            GridItemProps={{ xs: 12, md: 6 }}
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
                select
                name='categories_id'
                value={watch('categories_id')}
                label='Categorias'
                fullWidth
                variant='outlined'
                margin='normal'
                onChange={(e) => {
                    setValue('categories_id', e.target.value);
                }}
                SelectProps={{
                    multiple: true
                }}
                disabled={loading}
                error={errors.categories_id !== undefined}
                helperText={errors.categories_id && errors.categories_id['message']}
                InputLabelProps={{ shrink: true }}

            >
                <MenuItem value='' disabled>
                    <em>Selecione categorias</em>
                </MenuItem>
                {
                    categories.map(
                        (category: any, key) => (
                            <MenuItem key={key} value={category.id}> {category.name}</MenuItem>
                        )
                    )
                }
            </TextField>
            <SubmitActions
                disableButtons={loading}
                handleSave={() =>
                    triggerValidation().then(isValid => {
                        isValid && onSubmit(getValues(), null)
                    })
                }
            />
        </DefaultForm>
    );
};