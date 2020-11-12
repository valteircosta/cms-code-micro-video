// @flow 
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import * as React from 'react';
import { useForm } from 'react-hook-form';

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
    const { register, getValues } = useForm()

    return (
        <form>
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
            />
            Ativo?
            <Box dir={'rtl'} >
                <Button {...buttonProps}>Salvar</Button>
                <Button {...buttonProps} type='submit' >Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};