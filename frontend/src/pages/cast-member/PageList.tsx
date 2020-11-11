// @flow 
import { Box, Fab } from '@material-ui/core';
import * as React from 'react';
import { Link } from 'react-router-dom';
import { Page } from '../../components/Page';
import AddIcon from '@material-ui/icons/Add';
import Table from './Table';

const PageList = () => {
    return (
        /* cSpell:disable */
        <Page title={'Listagem de membros de elenco'}>
            {/* cSpell:enable */}

            <Box
                dir={'rtl'}>
                <Fab
                    /* cSpell:disable */
                    title='Adiciona membro de elenco'
                    /* cSpell:enabldisable */
                    size='small'
                    component={Link}
                    to='/cast-members/create'
                >
                    <AddIcon />
                </Fab>
            </Box>
            <Box>
                <Table />
            </Box>
        </Page>
    );
}
export default PageList;