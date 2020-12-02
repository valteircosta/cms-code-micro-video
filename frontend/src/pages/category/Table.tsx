// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from '../../util/http/category-http';
import { BadgeNo, BadgeYes } from '../../components/Badge';

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


interface Category {
    id: string;
    name: string;
}

type Props = {};

const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);
    // ComponentDidMount
    useEffect(() => {
        // Used in cleanup function for no happen error in load   
        let isSubscribed = true;
        (async () => {
            const { data } = await categoryHttp.list<{ data: Category[] }>();
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
            title='Listagem de categorias'
            data={data}
            columns={columnsDefinitions}
        />
    );
};
export default Table;