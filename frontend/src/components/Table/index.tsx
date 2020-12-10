// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps } from 'mui-datatables';
import { cloneDeep, merge, omit } from 'lodash';
import { MuiThemeProvider, Theme, useMediaQuery, useTheme } from '@material-ui/core';

export interface TableColumn extends MUIDataTableColumn {
    width?: string;
};
const makeDefaultOptions: MUIDataTableOptions = {
    /* spell-checker: disable */
    print: false,
    download: false,
    textLabels: {
        body: {
            noMatch: "Nenhum registro encontrado",
            toolTip: "Classificar",
        },
        pagination: {
            next: "Próxima página",
            previous: "Página anterior",
            rowsPerPage: "Por página",
            displayRows: "de",
        },
        toolbar: {
            search: "Busca",
            downloadCsv: "Download CSV",
            print: "Imprimir",
            viewColumns: "Ver Colunas",
            filterTable: "Filtrar Tables",
        },
        filter: {
            all: "Todos",
            title: "FILTROS",
            reset: "LIMPAR",
        },
        viewColumns: {
            title: "Ver Colunas",
            titleAria: "Ver/Esconder Colunas da Tabela",
        },
        selectedRows: {
            text: "registro(s) selecionados",
            delete: "Excluir",
            deleteAria: "Excluir regitros selecionados",
        },
    },
    // customSearchRender: (searchText: string,
    //     handleSearch: any,
    //     hideSearch: any,
    //     options: any) => {
    //     return <DebouncedTableSearch
    //         searchText={searchText}
    //         onSearch={handleSearch}
    //         onHide={hideSearch}
    //         options={options}
    //         debounceTime={debouncedSearchTime}
    //     />
    // }
};
/* spell-checker: enable */
export interface TableProps extends MUIDataTableProps {
    columns: TableColumn[];
    loading?: boolean;
};
const Table: React.FC<TableProps> = (props: TableProps) => {

    function extractMuiDataTableColumns(columns: TableColumn[]): MUIDataTableColumn[] {
        setColumnsWidth(columns);
        return columns.map(column => omit(column, 'width'));
    }
    /** Used by set value width column */
    function setColumnsWidth(columns: TableColumn[]) {
        columns.forEach((column, key) => {
            if (column.width) {
                const overrides = theme.overrides as any;
                overrides.MUIDataTableHeadCell.fixedHeader[`&:nth-child(${key + 2})`] = {
                    width: column.width
                }
            }
        })
    };

    /** Change message show during data load event */
    function applyLoading() {
        const textLabels = (newProps.options as any).textLabels;
        textLabels.body.noMatch = newProps.loading === true
            ? 'Carregando dados...'
            : textLabels.body.noMatch;
    }
    /** Change responsibility type by size screen*/
    function applyResponsive() {
        newProps.options.responsive = isSmOrDown ? 'standard' : 'vertical';
    }
    /**Remove all no original properties */
    function getOriginalMuiDataTableProps() {
        return omit(newProps, 'loading');
    }
    /** Get deep clone of the object theme Global for keep only local the change */
    const theme = cloneDeep<Theme>(useTheme());

    /** Detecting screen size change using hook useMediaQuery */
    const isSmOrDown = useMediaQuery(theme.breakpoints.down('sm'));

    /** Using lodash we are making merge the properties of all objects passed by params  */
    const newProps = merge(
        { options: cloneDeep(makeDefaultOptions) },
        props,
        { columns: extractMuiDataTableColumns(props.columns) },
    );

    /** Call apply */
    applyLoading();
    applyResponsive();
    const originalProps = getOriginalMuiDataTableProps();
    return (
        /** Set local theme defined above */
        <MuiThemeProvider theme={theme} >
            <MUIDataTable{...originalProps} />
        </MuiThemeProvider>
    );
};
export default Table;

export function makeActionStyle(column) {
    return theme => {
        const copyTheme = cloneDeep(theme);
        const selector = `&[data-testid^="MuiDataTableBodyCell-${column}"]`;
        (copyTheme.overrides as any).MUIDataTableBodyCell.root[selector] = {
            paddingTop: '0px',
            paddingBottom: '0px'
        };
        return copyTheme;
    }
};