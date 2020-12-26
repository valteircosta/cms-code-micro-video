import { MUIDataTableColumn } from "mui-datatables";
import { useState, useReducer, Dispatch, Reducer, useEffect } from "react";
import reducer, { Creators, INITIAL_STATE } from "../store/filter";
import {
  Actions as FilterActions,
  State as FilterState,
} from "../store/filter/types";
import { useDebounce } from "use-debounce";
import { useHistory } from "react-router";
import { History } from "history";
import { isEqual } from "lodash";
import * as yup from "../util/vendor/yup";
import { MuiDataTableRefComponent } from "../components/Table";

interface FilterManagerOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
  history: History;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;
}
interface useFilterOptions extends Omit<FilterManagerOptions, "history"> {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;
}
export default function useFilter(options: useFilterOptions) {
  console.log("useFilter");
  const history = useHistory();
  const filterManager = new FilterManager({ ...options, history });
  // Get  the state of the URL
  const INITIAL_STATE = filterManager.getStateFromURL() as any;
  const [filterState, dispatch] = useReducer<
    Reducer<FilterState, FilterActions>
  >(reducer, INITIAL_STATE);
  const [debouncedFilterState] = useDebounce(filterState, options.debounceTime);
  const [totalRecords, setTotalRecords] = useState<number>(0);
  filterManager.state = filterState;
  filterManager.debouncedState = debouncedFilterState;
  filterManager.dispatch = dispatch;
  filterManager.applyOrderInColumns();
  useEffect(() => {
    filterManager.replaceHistory();
  }, []);

  return {
    columns: filterManager.columns,
    filterManager,
    filterState,
    debouncedFilterState,
    dispatch,
    totalRecords,
    setTotalRecords,
  };
}

/**
 * Class for manager all search system
 */
export class FilterManager {
  schema;
  state: FilterState = null as any;
  debouncedState: FilterState = null as any;
  dispatch: Dispatch<FilterActions> = null as any;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  history: History;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;

  constructor(options: FilterManagerOptions) {
    const {
      columns,
      rowsPerPage,
      rowsPerPageOptions,
      history,
      tableRef,
    } = options;
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.rowsPerPageOptions = rowsPerPageOptions;
    this.history = history;
    this.tableRef = tableRef;
    this.createValidationSchema();
  }
  private resetTablePagination() {
    this.tableRef.current.changeRowsPerPage(this.rowsPerPage);
    this.tableRef.current.changePage(0);
  }
  changeSearch(value: any) {
    this.dispatch(Creators.setSearch({ search: value }));
  }
  changePage(page: number) {
    this.dispatch(Creators.setPage({ page: page + 1 }));
  }
  changeRowsPerPage(perPage: number) {
    this.dispatch(Creators.setPerPage({ per_page: perPage }));
  }
  changeColumnSort(changedColumn: string, direction: string) {
    this.dispatch(
      Creators.setSortOrder({
        name: changedColumn,
        direction: direction,
      })
    );
    this.resetTablePagination();
  }
  resetFilter() {
    const INITIAL_STATE = {
      ...this.schema.cast({}),
      search: {
        value: null,
        update: true,
      },
    };
    this.dispatch(Creators.setReset({ state: INITIAL_STATE }));
    this.resetTablePagination();
  }
  applyOrderInColumns() {
    // Find and map sortable column
    // Overriding columns object local
    this.columns = this.columns.map((column) => {
      return column.name === this.state.sortOrder.name
        ? //Add property sortDirection  of the object returned.
          {
            ...column,
            options: {
              ...column.options,
              sortOrder: this.state.sortOrder.direction,
            },
          }
        : column;
    });
  }
  // Clean object passed in search text
  cleanSearchText(text) {
    let newText = text;
    if (text && text.value !== undefined) {
      newText = text.value;
    }
    return newText;
  }
  replaceHistory() {
    this.history.replace({
      pathname: this.history.location.pathname,
      search: "?" + new URLSearchParams(this.formatSearchParams() as any),
      state: this.debouncedState,
    });
  }
  //Manipulate the state history
  pushHistory() {
    console.log("pushHistory");
    const newLocation = {
      pathname: this.history.location.pathname,
      search: "?" + new URLSearchParams(this.formatSearchParams() as any),
      state: {
        ...this.debouncedState,
        search: this.cleanSearchText(this.state.search),
      },
    };
    const oldState = this.history.location.state;
    const newState = this.debouncedState;

    if (isEqual(newState, oldState)) {
      console.log("isEquals");
      return;
    }
    this.history.push(newLocation);
  }
  private formatSearchParams() {
    const search = this.cleanSearchText(this.debouncedState.search);
    return {
      ...(search && search !== "" && { search: search }),
      ...(this.debouncedState.pagination.page !== 1 && {
        page: this.debouncedState.pagination.page,
      }),
      ...(this.debouncedState.pagination.per_page !== 15 && {
        per_page: this.debouncedState.pagination.per_page,
      }),
      ...(this.debouncedState.sortOrder.name && {
        name: this.debouncedState.sortOrder.name,
        direction: this.debouncedState.sortOrder.direction,
      }),
    };
  }

  public getStateFromURL() {
    const queryParams = new URLSearchParams(
      this.history.location.search.substr(1)
    );
    return this.schema.cast({
      search: queryParams.get("search"),
      pagination: {
        page: queryParams.get("page"),
        per_page: queryParams.get("per_page"),
      },
      sortOrder: {
        name: queryParams.get("name"),
        direction: queryParams.get("direction"),
      },
    });
  }
  private createValidationSchema() {
    this.schema = yup.object().shape({
      search: yup
        .string()
        .transform((value) => (!value ? undefined : value))
        .default(""),
      pagination: yup.object().shape({
        page: yup
          .number()
          .transform((value) =>
            isNaN(value) || parseInt(value) < 1 ? undefined : value
          )
          .default(1),
        per_page: yup
          .number()
          .transform((value) => ( isNaN(value) || !this.rowsPerPageOptions.includes(parseInt(value)) ? undefined : value))
          .default(this.rowsPerPage),
      }),
      sortOrder: yup.object().shape({
        name: yup
          .string()
          .nullable()
          .transform((value) => {
            const columnsName = this.columns
              .filter(
                (column) => !column.options || column.options.sort !== false
              )
              .map((column) => column.name);
            return columnsName.includes(value) ? value : undefined;
          })
          .default(null),
        direction: yup
          .string()
          .nullable()
          .transform((value) =>
            !value || !["asc", "desc"].includes(value.toLowerCase())
              ? undefined
              : value
          )
          .default(null),
      }),
      // ...(this.extraFilter && {
      //   extraFilter: this.extraFilter.createValidationSchema(),
      // }),
    });
  }
}
