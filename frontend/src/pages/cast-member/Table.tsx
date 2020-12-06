// @flow 
import * as React from 'react';
import { useState, useEffect } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import castMemberHttp from '../../util/http/cast-member-http';
import { CastMember, ListResponse } from '../../util/models';
import DefaultTable, { TableColumn } from '../../components/Table';
import { useSnackbar } from 'notistack';

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

const columnsDefinitions: TableColumn[] = [
    {
        name: 'id',
        label: 'ID',
        width: '25%',
        options: {
            sort: false,
        }
    },
    {
        name: 'name',
        label: 'Nome',
        width: '40%',
    },
    {
        name: 'type',
        label: 'Tipo',
        width: '12%',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return CastMemberTypeMap[value];
            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        width: '10%',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
            }
        }
    },
    {
        name: 'actions',
        label: 'Ações',
        width: '13%'
    },


];


type Props = {

};
const Table = (props: Props) => {

    const snackbar = useSnackbar();
    const [data, setData] = useState<CastMember[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    useEffect(() => {
        let isSubscribed = true;
        (async function getCastMember() {
            setLoading(true);
            try {
                const { data } = await castMemberHttp.list<ListResponse<CastMember>>();
                if (isSubscribed) {
                    setData(data.data);
                };

            } catch (error) {
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    { variant: 'error' }
                );
            } finally {
                setLoading(false);
            }
        })(); //IIFE by call
        return () => {
            isSubscribed = false;
        };
    }, [snackbar]);

    return (
        <DefaultTable
            title='Listagem de membros de elenco'
            data={data}
            columns={columnsDefinitions}
            loading={loading}
        />
    );
};
export default Table;