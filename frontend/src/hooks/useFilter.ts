import { MUIDataTableColumn } from "mui-datatables";
import { useState, useReducer, Dispatch, Reducer } from "react";
import reducer, { INITIAL_STATE } from "../store/filter";
import {
  Actions as FilterActions,
  State as FilterState,
} from "../store/filter/types";

interface FilterManagerOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
}
export default function useFilter(options: FilterManagerOptions) {
  console.log("useFilter");
  const filterManager = new FilterManager(options);

  // Get  the state of the URL
  const [filterState, dispatch] = useReducer<
    Reducer<FilterState, FilterActions>
  >(reducer, INITIAL_STATE);
  const [totalRecords, setTotalRecords] = useState<number>(0);
  filterManager.state = filterState;
  filterManager.dispatch = dispatch;
  return {
    filterManager,
    filterState,
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
  debounceTime: number;

  constructor(options: FilterManagerOptions) {
    const { columns, rowsPerPage, rowsPerPageOptions, debounceTime } = options;
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.rowsPerPageOptions = rowsPerPageOptions;
    this.debounceTime = debounceTime;
  }
}
