// @flow 
import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import genreHttp from '../../util/http/genre-http';
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import { Genre, ListResponse } from '../../util/models';
import DefaultTable, { makeActionStyle, MuiDataTableRefComponent, TableColumn } from '../../components/Table';
import { useSnackbar } from 'notistack';
import { IconButton, MuiThemeProvider } from '@material-ui/core';
import { Link } from 'react-router-dom';
import EditIcon from '@material-ui/icons/Edit';
import useFilter from '../../hooks/useFilter';
import { FilterResetButton } from '../../components/Table/FilterResetButton';




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

const debounceTime = 300;
const debouncedSearchTime = 300;
const rowsPerPage = 15;
const rowsPerPageOptions = [15, 25, 50];


const Table = () => {

    const snackbar = useSnackbar();
    const subscribed = useRef(true);
    const [data, setData] = useState<Genre[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
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
        debouncedFilterState.sortOrder,
    ]);

    async function getData() {
        setLoading(true);
        try {
            const { data } = await genreHttp.list<ListResponse<Genre>>({
                queryParams: {
                    search: filterManager.cleanSearchText(filterState.search),
                    page: filterState.pagination.page,
                    per_page: filterState.pagination.per_page,
                    sort: filterState.sortOrder.name,
                    dir: filterState.sortOrder.direction,
                }

            });

            if (subscribed.current) {
                setData(data.data);
                setTotalRecords(data.meta.total);
            };
        } catch (error) {

            console.error(error);
            if (genreHttp.isCancelledRequest(error)) {
                return
            }
            snackbar.enqueueSnackbar(
                'Não foi possível carregar as informações',
                { variant: 'error' }
            );
        } finally {
            setLoading(false);
        }
    }
  
 return (
        <MuiThemeProvider theme={makeActionStyle(columnsDefinitions.length - 1)} >
            <DefaultTable
                title='Listagem de gêneros'
                data={data}
                columns={columns}
                loading={loading}
                debouncedSearchTime={debouncedSearchTime}
                ref={tableRef}
                options={{
                    serverSide: true,
                    responsive: 'standard',
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