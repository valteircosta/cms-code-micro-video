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
        <Page title={'Listagem de categorias'}>
            {/* cSpell:enable */}

            <Box
                dir={'rtl'} paddingBottom={2}>
                <Fab
                    /* cSpell:disable */
                    title='Adiciona categoria'
                    color='secondary'
                    /* cSpell:enable */
                    size='small'
                    component={Link}
                    to='/categories/create'
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