// @flow 
import * as React from 'react';
import { useState, useEffect, useRef, useReducer } from 'react';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from '../../util/http/category-http';
import { BadgeNo, BadgeYes } from '../../components/Badge';
import { Category, ListResponse } from '../../util/models';
import DefaultTable, { makeActionStyle, TableColumn, MuiDataTableRefComponent } from '../../components/Table';
import { useSnackbar } from 'notistack';
import { IconButton, MuiThemeProvider } from '@material-ui/core';
import { Link } from 'react-router-dom';
import EditIcon from '@material-ui/icons/Edit';
import { FilterResetButton } from '../../components/Table/FilterResetButton';
import DebouncedTableSearch from '../../components/Table/DebouncedTableSearch';
import { Creators } from '../../store/filter';
import useFilter from '../../hooks/useFilter';

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
            filter:false
        }
    },
    {
        name: 'name',
        label: 'Nome',
        width: '43%',
        options: {
            filter:false
        }
 
    },
    {
        name: 'is_active',
        label: 'Ativo?',
        width: '4%',
        options: {
            filterOptions: {
                names: ['Sim', 'Não']
              },
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
            filter:false,
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
            filter:false,
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

const debounceTime = 300;
const debouncedSearchTime = 300;
const rowsPerPage = 15;
const rowsPerPageOptions = [15, 25, 50];
const Table = () => {


    const snackbar = useSnackbar();
    // useRef hook make object content property current = {current:true} 
    const subscribed = useRef(true);
    const [data, setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    // Piking up reference of the component React
    const tableRef = useRef() as React.MutableRefObject<MuiDataTableRefComponent>;
    const {
        columns,
        filterManager,
        filterState,
        debouncedFilterState,
        dispatch,
        totalRecords,
        setTotalRecords,

    } = useFilter({
        columns: columnsDefinitions,
        debounceTime: debounceTime,
        rowsPerPage: rowsPerPage,
        rowsPerPageOptions: rowsPerPageOptions,
        tableRef: tableRef,
    });
    // ComponentDidMount
    useEffect(() => {
        subscribed.current = true;
        filterManager.pushHistory();
        getData();
        // Cleanup function
        return () => {
            subscribed.current = false;
        };

    }, [
        filterManager.cleanSearchText(debouncedFilterState.search),
        debouncedFilterState.pagination.page,
        debouncedFilterState.pagination.per_page,
        debouncedFilterState.order,
    ]);

    async function getData() {
        setLoading(true);
        try {
            const { data } = await categoryHttp.list<ListResponse<Category>>({
                queryParams: {
                    search: filterManager.cleanSearchText(debouncedFilterState.search),
                    page: debouncedFilterState.pagination.page,
                    per_page: debouncedFilterState.pagination.per_page,
                    sort: debouncedFilterState.order.sort,
                    dir: debouncedFilterState.order.dir,
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

    return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinitions.length - 1)} >
            <DefaultTable
                title='Listagem de categorias'
                data={data}
                columns={columns}
                loading={loading}
                debouncedSearchTime={debouncedSearchTime}
                ref={tableRef}
                options={{
                    serverSide: true,
                    responsive: 'scrollMaxHeight',
                    searchText: filterState.search as string,
                    page: filterState.pagination.page - 1,
                    rowsPerPage: filterState.pagination.per_page,
                    rowsPerPageOptions: rowsPerPageOptions,
                    count: totalRecords,
                    customToolbar: () => (
                        <FilterResetButton
                            handleClick={() => filterManager.resetFilter()}
                        />
                    ),
                    onSearchChange: (value: any) => filterManager.changeSearch(value),
                    onChangePage: (page: number) => filterManager.changePage(page),
                    onChangeRowsPerPage: (perPage: number) => filterManager.changeRowsPerPage(perPage),
                    onColumnSortChange: (changedColumn: string, direction: string) =>
                        filterManager.changeColumnSort(changedColumn, direction),
                    // customSearchRender: (
                    //     searchText: string,
                    //     handleSearch: any,
                    //     hideSearch: any,
                    //     options: any,
                    // ) => {
                    //     return <DebouncedTableSearch
                    //         searchText={searchText}
                    //         onSearch={handleSearch}
                    //         onHide={hideSearch}
                    //         options={options}
                    //     //  debounceTime={debouncedSearchTime}
                    //     />
                    // },


                }}
            />
        </MuiThemeProvider>

    );
};
export default Table;