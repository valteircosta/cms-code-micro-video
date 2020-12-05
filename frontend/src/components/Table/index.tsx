// @flow 
import * as React from 'react';
import MUIDataTable, { MUIDataTableOptions, MUIDataTableProps } from 'mui-datatables';
import { merge } from 'lodash';
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
interface TableProps extends MUIDataTableProps {

};
const Table: React.FC<TableProps> = (props: TableProps) => {
    /** Using lodash we are making merge the properties of all objects passed by params  */
    const newProps = merge({ options: makeDefaultOptions }, props);
    return (
        <MUIDataTable{...newProps} />
    );
};
export default Table;