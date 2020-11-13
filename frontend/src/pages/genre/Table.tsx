// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'categories',
        label: 'Categorias',
        options: {
            customBodyRender: (value, tableMeta, updateValue) =>
                value.map((value: { name: string }) => value.name).join(', ')
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
            }
        }
    },
];


type Props = {

};

interface Genre {
    id: string;
    name: string;
}
const Table = (props: Props) => {

    const [data, setData] = useState<Genre[]>([]);

    useEffect(() => {
        
        
        httpVideo.get('genres').then(
            (response) => setData(response.data.data)
        )
    }, []);

    return (
        <MUIDataTable
            title='Listagem de gêneros'
            data={data}
            columns={columnsDefinitions}
        />
    );
};
export default Table;