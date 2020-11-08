// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'is_active',
        label: 'Ativo?'
    },
    {
        name: 'created_at',
        label: 'Criado em'
    },
];

const data = [
    { name: 'title', is_active: true, created_at: '2020-10-01' },
    { name: 'title1', is_active: false, created_at: '2020-10-02' },
    { name: 'title2', is_active: true, created_at: '2020-10-03' },
    { name: 'title3', is_active: false, created_at: '2020-10-04' },
    { name: 'title4', is_active: true, created_at: '2020-10-05' },
    { name: 'title5', is_active: false, created_at: '2020-10-06' },
    { name: 'title6', is_active: true, created_at: '2020-10-07' },
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