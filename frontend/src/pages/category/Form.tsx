// @flow 
import { Box, Button, ButtonProps, Checkbox, FormControlLabel, makeStyles, TextField, Theme } from '@material-ui/core';
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
        console.log(id);
        setLoading(true);
        categoryHttp.get(id)
            .then(({ data }) => {
                setCategory(data.data);
                reset(data.data);
            })
            .finally(() => setLoading(false))
    }, []);

    //Used for make bind between components, in case checkbox
    useEffect(() => {
        register({ name: 'is_active' })
    }, [register]);//Look [register] is dependence passed to hook

    function onSubmit(formData, event) {
        setLoading(true);
        const http = !category
            ? categoryHttp.create(formData)
            : categoryHttp.update(category.id, formData);
        console.log(event);
        http
            .then((response) => console.log(response))
            .finally(() => setLoading(false))
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