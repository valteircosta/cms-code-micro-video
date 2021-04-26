// @flow 
import * as React from 'react';
import { Page } from '../../components/Page';
import { Form } from './Form';
import { useParams } from 'react-router';

const PageForm = () => {
    const { id } = useParams <{id: string}> ();
    return (
        <Page title={!id ? 'Criar vídeo' : 'Editar vídeo'}>
            <Form />
        </Page>

    );
};

export default PageForm;