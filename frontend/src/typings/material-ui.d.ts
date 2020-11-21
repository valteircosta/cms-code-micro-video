/* cSpell:disable */
// Class room name Entendendo a definição de tipo no Typescript
/* cSpell:enable */

import { ComponentNameToClassKey } from "@material-ui/core/styles/overrides";
import { PaletteColor, PaletteOptions, Palette, PaletteColorOptions } from "@material-ui/core/styles/createPalette";
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
    MUIDataTablePagination: any;
  }
}

declare module "@material-ui/core/styles/createPalette" {
  import { PaletteColorOptions } from "@material-ui/core/styles";
   interface Palette {
    success: PaletteColor
  }
  interface PaletteOptions {
    success?: PaletteColorOptions
  }
}
