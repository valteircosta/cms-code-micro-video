// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import genreHttp from '../../util/http/genre-http';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import { Genre, ListResponse } from '../../util/models';

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

const Table = (props: Props) => {

    const [data, setData] = useState<Genre[]>([]);

    useEffect(() => {
        // Used in cleanup function for no happen error in load   
        let isSubscribed = true;
        (async () => {
            const { data } = await genreHttp.list<ListResponse<Genre>>();

            if (isSubscribed) {
                setData(data.data);
            };
        })();
        // Cleanup function
        return () => {
            isSubscribed = false;
        };
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