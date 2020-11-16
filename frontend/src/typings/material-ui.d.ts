/* cSpell:disable */
// Class room name Entendendo a definição de tipo no Typescript
/* cSpell:enable */

import { ComponentNameToClassKey } from "@material-ui/core/styles/overrides";

declare module "@material-ui/core/styles/overrides" {
  interface ComponentNameToClassKey {
    MUIDataTable: any;
    MUIDataTableToolbar: any;
    MUIDataTableHeadCell: any;
    MUIDataTableSortLabel: any;
    MUIDataTableSelectCell: any;
    MUIDataTableBodyCell: any;
    MUIDataTableToolbarSelect: any;
    MUIDataTableBodyRow: any;
    MuiTablePagination: any;
  }
}
