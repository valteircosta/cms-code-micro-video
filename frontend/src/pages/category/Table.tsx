// @flow 
import * as React from 'react';
import { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from '../../util/http/category-http';
import { BadgeNo, BadgeYes } from '../../components/Badge';
import { Category, ListResponse } from '../../util/models';
import DefaultTable from '../../components/Table';


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
                return value ? <BadgeYes /> : <BadgeNo />;
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

type Props = {};

const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);
    // ComponentDidMount
    useEffect(() => {
        // Used in cleanup function for no happen error in load   
        let isSubscribed = true;
        (async () => {
            const { data } = await categoryHttp.list<ListResponse<Category>>();
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

        <DefaultTable
            title='Listagem de categorias'
            data={data}
            columns={columnsDefinitions}
        />
    );
};
export default Table;