// @flow 
import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from '../../util/http/category-http';
import { BadgeNo, BadgeYes } from '../../components/Badge';
import { Category, ListResponse } from '../../util/models';
import DefaultTable, { makeActionStyle, TableColumn } from '../../components/Table';
import { useSnackbar } from 'notistack';
import { IconButton, MuiThemeProvider } from '@material-ui/core';
import { Link } from 'react-router-dom';
import EditIcon from '@material-ui/icons/Edit';

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
        width: '43%',

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
        width: '13%',
        options: {
            sort: false,
            customBodyRender(value, tableMeta) {
                return (
                    <IconButton
                        color={'secondary'}
                        component={Link}
                        to={`/categories/${tableMeta.rowData[0]}/edit`}
                    >
                        <EditIcon />
                    </IconButton>
                )
            }
        }
    },

];

interface Pagination {
    page: number;
    total: number;
    per_page: number;
};

interface Order {
    sort: string | null;
    dir: string | null;
};
interface SearchState {
    search: string;
    pagination: Pagination;
    order: Order;
};
type Props = {};

const Table = (props: Props) => {

    const snackbar = useSnackbar();
    // useRef hook make object content property current = {current:true} 
    const subscribed = useRef(true);
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [searchState, setSearchState] = useState<SearchState>({
        search: '',
        pagination: {
            page: 1,
            total: 0,
            per_page: 10
        },
        order: {
            sort: null,
            dir: null,
        }
    });
    // Find and map sortable column 
    const column = columnsDefinitions.map((column: any) => {
        if (column.name === searchState.order.sort) {
            column.options.sortDirection = searchState.order.dir

        }
    });
    // ComponentDidMount
    useEffect(() => {
        subscribed.current = true;
        getData();
        // Cleanup function
        return () => {
            subscribed.current = false;
        };

    }, [
        searchState.search,
        searchState.pagination.page,
        searchState.pagination.per_page,
        searchState.order,
    ]);

    async function getData() {
        setLoading(true);
        try {
            const { data } = await categoryHttp.list<ListResponse<Category>>({
                queryParams: {
                    search: searchState.search,
                    page: searchState.pagination.page,
                    per_page: searchState.pagination.per_page,
                    sort: searchState.order.sort,
                    dir: searchState.order.dir,
                }
            });
            console.log(subscribed);
            if (subscribed.current) {
                setData(data.data);
                setSearchState((prevState => ({
                    ...prevState,
                    pagination: {
                        ...prevState.pagination,
                        total: data.meta.total
                    }
                })));
            };
        } catch (error) {
            snackbar.enqueueSnackbar(
                'Não foi possível carregar as informações',
                { variant: 'error' }
            );

        } finally {
            setLoading(false);
        }

    };
    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinitions.length - 1)} >
            <DefaultTable
                title='Listagem de categorias'
                data={data}
                columns={columnsDefinitions}
                loading={loading}
                options={{
                    serverSide: true,
                    responsive: 'standard',
                    searchText: searchState.search as string,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    onSearchChange: (value: any) => setSearchState((prevState => (
                        {
                            ...prevState,
                            search: value
                        }
                    ))),
                    onChangePage: (page: number) => setSearchState((prevState => (
                        {
                            ...prevState,
                            pagination: {
                                ...prevState.pagination,
                                page: page + 1

                            }
                        }
                    ))),
                    onChangeRowsPerPage: (perPage: number) => setSearchState((prevState => (
                        {
                            ...prevState,
                            pagination: {
                                ...prevState.pagination,
                                per_page: perPage

                            }
                        }
                    ))),
                    onColumnSortChange: (changedColumn: string, direction: string) => setSearchState((prevState => (
                        {
                            ...prevState,
                            order: {
                                sort: changedColumn,
                                dir: direction,
                            }
                        }
                    ))),
                }}
            />
        </MuiThemeProvider>

    );
};
export default Table;