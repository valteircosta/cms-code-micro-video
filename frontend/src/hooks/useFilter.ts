import { MUIDataTableColumn } from "mui-datatables";
import { useState, useReducer, Dispatch, Reducer } from "react";
import reducer, { Creators, INITIAL_STATE } from "../store/filter";
import {
  Actions as FilterActions,
  State as FilterState,
} from "../store/filter/types";
import { useDebounce } from "use-debounce";
import { useHistory } from "react-router";
import { History } from "history";

interface FilterManagerOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
  history: History;
}
interface useFilterOptions extends Omit<FilterManagerOptions, "history"> {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
}
export default function useFilter(options: useFilterOptions) {
  console.log("useFilter");
  const history = useHistory();
  const filterManager = new FilterManager({ ...options, history });
  // Get  the state of the URL
  const [filterState, dispatch] = useReducer<
    Reducer<FilterState, FilterActions>
  >(reducer, INITIAL_STATE);
  const [debouncedFilterState] = useDebounce(filterState, options.debounceTime);
  const [totalRecords, setTotalRecords] = useState<number>(0);
  filterManager.state = filterState;
  filterManager.dispatch = dispatch;
  filterManager.applyOrderInColumns();

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
  state: FilterState = null as any;
  dispatch: Dispatch<FilterActions> = null as any;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  history: History;

  constructor(options: FilterManagerOptions) {
    const { columns, rowsPerPage, rowsPerPageOptions, history } = options;
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.rowsPerPageOptions = rowsPerPageOptions;
    this.history = history;
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
  //Manipulate the state history
  pushHistory() {
    const newLocation = {
      pathname: this.history.location.pathname,
      search: "?" + new URLSearchParams(this.formatSearchParams() as any),
      state: {
        ...this.state,
        search: this.cleanSearchText(this.state.search),
      },
    };
    this.history.push(newLocation);
  }
  private formatSearchParams() {
    const search = this.cleanSearchText(this.state.search);
    return {
      ...(search && search !== "" && { search: search }),
      ...(this.state.pagination.page !== 1 && {
        page: this.state.pagination.page,
      }),
      ...(this.state.pagination.per_page !== 15 && {
        per_page: this.state.pagination.per_page,
      }),
      ...(this.state.sortOrder.name && {
        name: this.state.sortOrder.name,
        direction: this.state.sortOrder.direction,
      }),
    };
  }
}
