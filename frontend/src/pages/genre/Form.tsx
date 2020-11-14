// @flow 

import React, { useEffect, useState } from 'react';
import { Box, Button, ButtonProps, makeStyles, MenuItem, TextField, Theme } from '@material-ui/core';
import { useForm } from 'react-hook-form';
import genreHttp from '../../util/http/genre-http';
import categoryHttp from '../../util/http/category-http';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

export const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: 'outlined',
    }
    //Using hook useState
    const [categories, setCategories] = useState<any>([]);
    //Using component react-hook-form 
    const { register, handleSubmit, getValues, setValue, watch } = useForm({
        defaultValues: {
            categories_id: []
        }
    });


    //Used for make bind between components
    useEffect(() => {
        register({ name: 'categories_id' })
    }, [register]);//Look [register] is dependence passed to hook

    useEffect(() => {
        categoryHttp
            .list()
            .then(({ data }) => setCategories(data.data))
    });

    function onSubmit(formData, event) {
        genreHttp
            .create(formData)
            .then((response) => console.log(response))
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)} >
            <TextField
                name='name'
                label='Nome'
                fullWidth
                variant='outlined'
                margin='normal'
                inputRef={register}
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
            >
                <MenuItem value='' disabled>
                    <em>Selecione categorias</em>
                </MenuItem>
                {
                    categories.map(
                        (category, key) => (
                            <MenuItem key={key} value={category.id}> {category.name} </MenuItem>
                        )
                    )
                }
            </TextField>
            <Box dir={'rtl'} >
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)} >Salvar</Button>
                <Button {...buttonProps} type='submit' >Salvar e continuar editando</Button>
            </Box>
        </form >
    );
};