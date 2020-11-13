// @flow 
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';
import castMemberHttp from '../../util/http/cast-member-http';

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

    //Using component react-hook-form 
    const { register, handleSubmit, getValues } = useForm();

    function onSubmit(formData, event) {
        castMemberHttp
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
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)} >Salvar</Button>
                <Button {...buttonProps} type='submit' >Salvar e continuar editando</Button>
            </Box>
        </form >
    );
};