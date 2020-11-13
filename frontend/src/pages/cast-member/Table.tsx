// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';

// With noImplicintAny = true must declare type
// const CastMemberTypeMap: { [key: number]: string } = {
//     1: "Diretor",
//     2: "Ator",
// };

const CastMemberTypeMap = {
    1: "Diretor",
    2: "Ator",
};

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'type',
        label: 'Tipo',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return CastMemberTypeMap[value];
            }
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

    const [data, setData] = useState([]);

    useEffect(() => {
        // categoryHttp
        //     .list<{ data: Category[] }>()
        //     .then(({ data }) => setData(data.data))
        httpVideo.get('cast_members').then(
            (response) => setData(response.data.data)
        )
    }, []);

    return (
        <MUIDataTable
            title='Listagem de membros de elenco'
            data={data}
            columns={columnsDefinitions}
        />
    );
};
export default Table;