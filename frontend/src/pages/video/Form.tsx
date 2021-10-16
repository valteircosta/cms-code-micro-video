// @flow 
import * as React from 'react';
import { Checkbox, FormControlLabel, TextField, Grid, Typography } from '@material-ui/core';
import { useForm } from 'react-hook-form';
import { useState, useEffect } from 'react';
import videoHttp from '../../util/http/video-http';
import * as yup from '../../util/vendor/yup';
import { useHistory, useParams } from 'react-router';
import { useSnackbar } from 'notistack';
import { Video } from '../../util/models';
import SubmitActions from '../../components/SubmitActions';
import { DefaultForm } from '../../components/DefaultForm';


/* cSpell:disable */
const validationSchema = yup.object().shape({
    title: yup.string()
        .label('Título')
        .required()
        .max(255),
    description: yup.string()
        .label('Sinopse')
        .required(),
    year_launched: yup.number()
        .label('Ano de lançamento')
        .required().min(1),
    duration: yup.number()
        .label('Duração')
        .required().min(1),
    rating: yup.string()
        .label('Classificação')
        .required(),
});
/* cSpell:disable */


export const Form = () => {

    //Using component react-hook-form 
    const { register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        triggerValidation,
    } = useForm({
        validationSchema,
        defaultValues: {
            title: '',
            description: '',
            year_launched: '',
            duration: '',
            is_Active: true
        }
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<{ id: string }>();
    const [video, setVideo] = useState<Video | null>(null);
    //Make state default value false
    const [loading, setLoading] = useState<boolean>(false);


    useEffect(() => {
        if (!id) {
            return;
        }
        // We can work with pattern IIFE (Immediately Invokable Function Expressions)
        let isSubscribed = true;
        (async function getCategory() {
            setLoading(true);
            try {
                const { data } = await videoHttp.get(id);
                if (isSubscribed) {
                    setVideo(data.data);
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

        })(); // Call with IIFE
        return () => {
            isSubscribed = false;
        }
    }, []);


    async function onSubmit(formData, event) {
        try {
            setLoading(true);
            const http = !video
                ? videoHttp.create(formData)
                : videoHttp.update(video.id, formData);
            const { data } = await http;
            snackbar.enqueueSnackbar(
                'Video salva com sucesso',
                { variant: 'success' }
            );
            setTimeout(() => {
                // Is event check button clicked
                event
                    ? (
                        id
                            //Has id is editing else add
                            ? history.replace(`/videos/${data.data.id}/edit`)
                            : history.push(`/videos/${data.data.id}/edit`)
                    )
                    : history.push('/videos')
            });
        } catch (error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não foi possível salvar a video',
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
            <Grid container spacing={5}>
                <Grid item xs={12} md={6}>

                    <TextField
                        name='title'
                        label='Título'
                        fullWidth
                        variant='outlined'
                        margin='normal'
                        inputRef={register}
                        disabled={loading}
                        error={errors.title !== undefined}
                        helperText={errors.title && errors.title.message}
                        InputLabelProps={{ shrink: true }}
                    />
                    <TextField
                        name='description'
                        label='Sinopse'
                        multiline
                        rows='4'
                        fullWidth
                        variant='outlined'
                        margin='normal'
                        inputRef={register}
                        disabled={loading}
                        InputLabelProps={{ shrink: true }}
                        error={errors.description !== undefined}
                        helperText={errors.description && errors.description.message}
                    />
                    <Grid container spacing={1}>
                        <Grid item xs={6} >
                            <TextField
                                name='year_launched'
                                label='Ano de lançamento'
                                type='number'
                                fullWidth
                                variant='outlined'
                                margin='normal'
                                inputRef={register}
                                disabled={loading}
                                InputLabelProps={{ shrink: true }}
                                error={errors.year_launched !== undefined}
                                helperText={errors.year_launched && errors.year_launched.message}
                            />
                        </Grid>
                        <Grid item xs={6} >
                            <TextField
                                name='duration'
                                label=''
                                type='number'
                                fullWidth
                                variant='outlined'
                                margin='normal'
                                inputRef={register}
                                disabled={loading}
                                InputLabelProps={{ shrink: true }}
                                error={errors.duration !== undefined}
                                helperText={errors.duration && errors.duration.message}
                            />
                        </Grid>
                    </Grid>
                    Elenco
                    <br />
                    Gêneros e categorias
                </Grid>

                <Grid item xs={12} md={6}>
                    Classificação
                    <br />
                Uploads
                <br />
                    <FormControlLabel
                        control={
                            <Checkbox
                                name='opened'
                                color='primary'
                                onChange={
                                    () => setValue('opened', !getValues()['opened'])
                                }
                                checked={watch('opened')}
                                disabled={loading}
                            />
                        }
                        label={<Typography color='primary' variant={'subtitle2'} >
                            Quero que este conteúdo apareça na sessão lançamentos
                            </Typography>

                        }
                        labelPlacement={'end'}
                    />

                </Grid>
            </Grid >
            <SubmitActions
                disableButtons={loading}
                handleSave={() =>
                    triggerValidation().then(isValid => {
                        onSubmit(getValues(), null)
                    })
                }
            />
        </DefaultForm >
    );
};