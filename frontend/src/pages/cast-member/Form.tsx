// @flow 

import React, { useEffect, useState } from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl,
    FormControlLabel,
    FormHelperText,
    FormLabel,
    makeStyles,
    Radio,
    RadioGroup,
    TextField,
    Theme
} from '@material-ui/core';
import { useForm } from 'react-hook-form';
import castMemberHttp from '../../util/http/cast-member-http';
import * as yup from '../../util/vendor/yup';
import { yupResolver } from '@hookform/resolvers/yup';
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
        .required()
        .max(255),
    type: yup.number()
        .label('Tipo')
        .required(),
});

export const Form = () => {


    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch
    } = useForm({
        defaultValues: {
            name: null,
            type: null
        },
        resolver: yupResolver(validationSchema),
    });

    const classes = useStyles();
    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [castMember, setCastMember] = useState<{ id: string } | null>(null);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);

    //Using component react-hook-form
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
        async function getCastMember() {
            setLoading(true);
            try {
                const { data } = await castMemberHttp.get(id);
                setCastMember(data.data);
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
        getCastMember();
    }, []);

    //Used for make bind between components
    useEffect(() => {
        register({ name: 'type' })
    }, [register]);//Look [register] is dependence passed to hook


    async function onSubmit(formData, event) {
        try {
            setLoading(true);
            const http = !castMember
                ? castMemberHttp.create(formData)
                : castMemberHttp.update(id, formData);
            const { data } = await http;
            snackbar.enqueueSnackbar(
                'Membro do elenco salvo com sucesso',
                { variant: 'success' }
            );
            setTimeout(() => {
                // Is event check button clicked
                event
                    ? (
                        id
                            //Has id is editing else add
                            ? history.replace(`/cast-members/${data.data.id}/edit`)
                            : history.push(`/cast-members/${data.data.id}/edit`)
                    )
                    : history.push('/cast-members')
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
                helperText={errors.name && errors.name.message}
                InputLabelProps={{ shrink: true }}
            />
            <FormControl
                margin='normal'
                error={errors.type !== undefined}
                disabled={loading} >
                <FormLabel component='legend'>Tipo</FormLabel>
                <RadioGroup
                    name='type'
                    onChange={(e) => {
                        //Linking with component react-hook-form
                        setValue('type', parseInt(e.target.value));
                    }}
                    value={watch('type') + ''}
                >
                    <FormControlLabel value='1' control={<Radio />} label='Diretor' />
                    <FormControlLabel value='2' control={<Radio />} label='Ator' />
                </RadioGroup>
                {
                    errors.type && <FormHelperText id='type-helper-text'>{errors.type.message} </FormHelperText>
                }
            </FormControl>
            <Box dir={'rtl'} >
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)} >Salvar</Button>
                <Button {...buttonProps} type='submit' >Salvar e continuar editando</Button>
            </Box>
        </form >
    );
};