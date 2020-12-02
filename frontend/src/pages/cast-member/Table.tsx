// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import castMemberHttp from '../../util/http/cast-member-http';

/* eslint-disable */
// With noImplicintAny = true must declare type
// const CastMemberTypeMap: { [key: number]: string } = {
//     1: "Diretor",
//     2: "Ator",
// };
/* eslint-enable */

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
interface CastMember {
    id: string;
    name: string;
}
const Table = (props: Props) => {

    const [data, setData] = useState<CastMember[]>([]);

    useEffect(() => {
        let isSubscribed = true;
        (async function getCastMember() {
            const { data } = await castMemberHttp.list<{ data: CastMember[] }>();
            if (isSubscribed) {
                setData(data.data);
            };
        })(); //IIFE by call
        return () => {
            isSubscribed = false;
        };
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