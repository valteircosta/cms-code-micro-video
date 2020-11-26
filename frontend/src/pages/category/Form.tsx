// @flow 
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';
import { useState, useEffect } from 'react';
import { yupResolver } from '@hookform/resolvers/yup';
import categoryHttp from '../../util/http/category-http';
import * as yup from '../../util/vendor/yup';
import { useParams } from 'react-router';

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

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: 'secondary',
        variant: 'contained',
    }

    //Using component react-hook-form 
    const { register, handleSubmit, getValues, errors, reset } = useForm({
        defaultValues: {
            name: null,
            is_active: true
        },
        resolver: yupResolver(validationSchema),
    });

    const { id } = useParams<{ id: string }>();
    const [category, setCategory] = useState(null);
    useEffect(() => {
        if (!id) {
            return;
        }
        console.log(id);
        categoryHttp.get(id)
            .then(({ data }) => {
                setCategory(data.data);
                reset(data.data);
            })
    }, []);

    function onSubmit(formData, event) {
        categoryHttp
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
                InputLabelProps={{ shrink: true }}
            />
            <Checkbox
                name='is_active'
                inputRef={register}
                defaultChecked
            />
            Ativo?
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