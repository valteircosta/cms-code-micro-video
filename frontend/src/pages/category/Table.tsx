// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import { Chip } from '@material-ui/core';

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'is_active',
        label: 'Ativo?',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <Chip label='Sim' color="primary" /> : <Chip label='Não' color="secondary" />;

            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em'
    },
];


type Props = {

};

const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        httpVideo.get('categories').then(
            (response) => setData(response.data.data)
        )
    }, []);

    return (
        <MUIDataTable
            title='Listagem de categorias'
            data={data}
            columns={columnsDefinitions}
        />
    );
};
export default Table;