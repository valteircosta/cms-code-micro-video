// @flow 
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';
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
        color: 'secondary',
        variant: 'contained',
    }

    //Using component react-hook-form 
    const { register, handleSubmit, getValues, errors } = useForm({
        defaultValues: {
            is_active: true
        }
    });

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
                inputRef={register({
                    required: 'O campo nome é requerido'
                })}
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