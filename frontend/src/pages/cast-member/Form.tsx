// @flow 

import React, { useEffect, useState } from 'react';
import {
    FormControl,
    FormControlLabel,
    FormHelperText,
    FormLabel,
    Grid,
    Radio,
    RadioGroup,
    TextField,
} from '@material-ui/core';
import { useForm } from 'react-hook-form';
import castMemberHttp from '../../util/http/cast-member-http';
import * as yup from '../../util/vendor/yup';
import { yupResolver } from '@hookform/resolvers/yup';
import { useHistory, useParams } from 'react-router';
import { useSnackbar } from 'notistack';
import { Category } from '../../util/models';
import SubmitActions from '../../components/SubmitActions';


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
        watch,
        errors,
        reset,
        trigger,
    } = useForm({
        defaultValues: {
            name: null,
            type: null
        },
        resolver: yupResolver(validationSchema),
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [castMember, setCastMember] = useState<Category | null>(null);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {
        if (!id) {
            return;
        }
        let isSubscribed = true;
        (async function getCastMember() {
            setLoading(true);
            try {
                const { data } = await castMemberHttp.get(id);
                if (isSubscribed) {
                    setCastMember(data.data);
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
            return () => {
                isSubscribed = false;
            }

        })(); // Call IIFE
    }, [id, reset, snackbar]);

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
                'Não foi possível salvar membro do elenco',
                { variant: 'error' }
            );
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)} >
            <Grid>
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
                <SubmitActions
                    disableButtons={loading}
                    handleSave={() =>
                        trigger().then(isValid => {
                            isValid && onSubmit(getValues(), null)
                        })
                    }
                />
            </Grid>
        </form >
    );
};