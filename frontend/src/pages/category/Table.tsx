// @flow 
import * as React from 'react';
import { useState, useEffect } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from '../../util/http/category-http';
import { BadgeNo, BadgeYes } from '../../components/Badge';
import { Category, ListResponse } from '../../util/models';
import DefaultTable, { TableColumn } from '../../components/Table';
import { useSnackbar } from 'notistack';

/**
 * Using type defined in component Table for definition the column with width property 
 */
const columnsDefinitions: TableColumn[] = [
    {
        name: 'id',
        label: 'ID',
        width: '30%',
        options: {
            sort: false,
        }
    },
    {
        name: 'name',
        label: 'Nome',
        width: '43%'
    },
    {
        name: 'is_active',
        label: 'Ativo?',
        width: '4%',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <BadgeYes /> : <BadgeNo />;
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

type Props = {};

const Table = (props: Props) => {

    const snackbar = useSnackbar();
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    // ComponentDidMount
    useEffect(() => {
        // Used in cleanup function for no happen error in load   
        let isSubscribed = true;
        (async () => {
            setLoading(true);
            try {
                const { data } = await categoryHttp.list<ListResponse<Category>>();
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
            title='Listagem de categorias'
            data={data}
            columns={columnsDefinitions}
            loading={loading}
        />
    );
};
export default Table;