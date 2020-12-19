// @flow 
import * as React from 'react';
import { useState, useEffect, useRef, useReducer } from 'react';
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
import { FilterResetButton } from '../../components/Table/FilterResetButton';
import DebouncedTableSearch from '../../components/Table/DebouncedTableSearch';
import reducer, { INITIAL_STATE, Creators } from '../../store/filter';

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
type Props = {};

const Table = (props: Props) => {


    const snackbar = useSnackbar();
    // useRef hook make object content property current = {current:true} 
    const subscribed = useRef(true);
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [filterState, dispatch] = useReducer(reducer, INITIAL_STATE);
    const [totalRecords, setTotalRecords] = useState<number>(0);
    //const [filterState, filterState] = useState<filterState>(initialState);

    // Find and map sortable column 
    const columns = columnsDefinitions.map((column) => {
        return (column.name === filterState.sortOrder.name)
            //Add property sortDirection  of the object returned.
            ? {
                ...column,
                options: {
                    ...column.options,
                    sortOrder: filterState.sortOrder.direction
                }
            } : column;
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
        filterState.search,
        filterState.pagination.page,
        filterState.pagination.per_page,
        filterState.sortOrder,
    ]);

    async function getData() {
        setLoading(true);
        try {
            const { data } = await categoryHttp.list<ListResponse<Category>>({
                queryParams: {
                    search: cleanSearchText(filterState.search),
                    page: filterState.pagination.page,
                    per_page: filterState.pagination.per_page,
                    sort: filterState.sortOrder.name,
                    dir: filterState.sortOrder.direction,
                }
            });
            if (subscribed.current) {
                setData(data.data);
                setTotalRecords(data.meta.total);
                // filterState((prevState => ({
                //     ...prevState,
                //     pagination: {
                //         ...prevState.pagination,
                //         total: data.meta.total
                //     }
                // })));
            };
        } catch (error) {
            console.error(error);
            if (categoryHttp.isCancelledRequest(error)) {
                return
            }
            snackbar.enqueueSnackbar(
                'Não foi possível carregar as informações',
                { variant: 'error' }
            );

        } finally {
            setLoading(false);
        }

    };

    // Clean object passed in search text
    function cleanSearchText(text) {
        let newText = text;
        if (text && text.value !== undefined) {
            newText = text.value;
        }
        return newText;
    }
    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinitions.length - 1)} >
            <DefaultTable
                title='Listagem de categorias'
                data={data}
                columns={columns}
                loading={loading}
                debouncedSearchTime={500}
                options={{
                    serverSide: true,
                    responsive: 'standard',
                    searchText: filterState.search as string,
                    page: filterState.pagination.page - 1,
                    rowsPerPage: filterState.pagination.per_page,
                    count: totalRecords,
                    customToolbar: () => (
                        <FilterResetButton
                            handleClick={() => dispatch(Creators.setReset())}
                        />
                    ),
                    onSearchChange: (value: any) => dispatch(Creators.setSearch({ search: value })),
                    onChangePage: (page: number) => dispatch(Creators.setPage({ page: page + 1 })),
                    onChangeRowsPerPage: (perPage: number) => dispatch(Creators.setPerPage({ per_page: perPage })),
                    onColumnSortChange: (changedColumn: string, direction: string) => dispatch(Creators.setSortOrder({
                        name: changedColumn,
                        direction: direction
                    })),
                    customSearchRender: (
                        searchText: string,
                        handleSearch: any,
                        hideSearch: any,
                        options: any,
                    ) => {
                        return <DebouncedTableSearch
                            searchText={searchText}
                            onSearch={handleSearch}
                            onHide={hideSearch}
                            options={options}
                        //  debounceTime={debouncedSearchTime}
                        />

                    },


                }}
            />
        </MuiThemeProvider>

    );
};
export default Table;