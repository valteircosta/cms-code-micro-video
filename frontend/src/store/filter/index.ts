import * as Typings from "./types";
import { createActions, createReducer } from "reduxsauce";
/**
 * Using reduxsauce will make types and constant action name 'SET_SEARCH']
 * Not will need  name type 'SetSearchAction' complete also the payload is added automatic
 */
export const { Types, Creators } = createActions<
  {
    SET_SEARCH: string;
    SET_PAGE: string;
    SET_PER_PAGE: string;
    SET_SORT_ORDER: string;
    SET_RESET: string;
  },
  {
    setSearch(
      payload: Typings.SetSearchAction["payload"]
    ): Typings.SetSearchAction;
    setPage(payload: Typings.SetPageAction["payload"]): Typings.SetPageAction;
    setPerPage(
      payload: Typings.SetPerPageAction["payload"]
    ): Typings.SetPerPageAction;
    setSortOrder(
      payload: Typings.SetSortOrderAction["payload"]
    ): Typings.SetSortOrderAction;
    setReset();
  }
>({
  setSearch: ["payload"],
  setPage: ["payload"],
  setPerPage: ["payload"],
  setSortOrder: ["payload"],
  setReset: [],
});
// Initial state of component
export const INITIAL_STATE: Typings.State = {
  search: "",
  pagination: {
    page: 1,
    per_page: 10,
  },
  sortOrder: {
    name: null,
    direction: null,
  },
};

const reducer = createReducer<Typings.State, Typings.Actions>(INITIAL_STATE, {
  [Types.SET_SEARCH]: setSearch as any,
  [Types.SET_PAGE]: setPage as any,
  [Types.SET_PER_PAGE]: setPerPage as any,
  [Types.SET_SORT_ORDER]: setSortOrder as any,
  [Types.SET_RESET]: setReset as any,
});
export default reducer;
function setSearch(
  state = INITIAL_STATE,
  action: Typings.SetSearchAction
): Typings.State {
  return {
    ...state,
    search: action.payload.search,
    /** Override pagination for back to page 1 */
    pagination: {
      ...state.pagination,
      page: 1,
    },
  };
}
function setPage(
  state = INITIAL_STATE,
  action: Typings.SetPageAction
): Typings.State {
  return {
    ...state,
    pagination: {
      ...state.pagination,
      page: action.payload.page,
    },
  };
}
function setPerPage(
  state = INITIAL_STATE,
  action: Typings.SetPerPageAction
): Typings.State {
  return {
    ...state,
    pagination: {
      ...state.pagination,
      per_page: action.payload.per_page,
    },
  };
}
function setSortOrder(
  state = INITIAL_STATE,
  action: Typings.SetSortOrderAction
): Typings.State {
  return {
    ...state,
    sortOrder: {
      name: action.payload.name,
      direction: action.payload.direction,
    },
  };
}
function setReset(state = INITIAL_STATE): Typings.State {
  return { ...INITIAL_STATE, search: { value: null, updated: true } };
}
