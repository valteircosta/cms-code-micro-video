// @flow 
import * as React from 'react';
import { useState, useEffect } from 'react';
import genreHttp from '../../util/http/genre-http';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import { Genre, ListResponse } from '../../util/models';
import DefaultTable, { TableColumn } from '../../components/Table';
import { useSnackbar } from 'notistack';
import { IconButton } from '@material-ui/core';
import { Link } from 'react-router-dom';
import EditIcon from '@material-ui/icons/Edit';




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
        width: '30%',
    },
    {
        name: 'categories',
        label: 'Categorias',
        width: '22%',
        options: {
            customBodyRender: (value, tableMeta, updateValue) =>
                value.map((value: { name: string }) => value.name).join(', ')
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
    },{
        name: 'actions',
        label: 'Ações',
        width: '13%',
        options: {
            sort: false,
            customBodyRender(value, tableMeta) {
                return (
                    <IconButton
                        color={'secondary'}
                        component={Link}
                        to={`/genres/${tableMeta.rowData[0]}/edit`}
                    >
                        <EditIcon />
                    </IconButton>
                )
            }
        }
    }
];


type Props = {

};

const Table = (props: Props) => {

    const snackbar = useSnackbar();
    const [data, setData] = useState<Genre[]>([]);
    const [loading, setLoading] = useState<boolean>(false);

    useEffect(() => {
        // Used in cleanup function for no happen error in load   
        let isSubscribed = true;
        (async () => {
            setLoading(true);
            try {

                const { data } = await genreHttp.list<ListResponse<Genre>>();

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

        })();
        // Cleanup function
        return () => {
            isSubscribed = false;
        };
    }, [snackbar]);

    return (
        <DefaultTable
            title='Listagem de gêneros'
            data={data}
            columns={columnsDefinitions}
            loading={loading}
        />
    );
};
export default Table;