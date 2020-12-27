import { AnyAction } from "redux";

export interface Pagination {
  page: number;
  per_page: number;
}

export interface SortOrder {
  name: string | null;
  direction: string | null;
}
export interface State {
  search: string | { value; [key: string]: any };
  pagination: Pagination;
  sortOrder: SortOrder;
  extraFilter?: { [key: string]: any };
}
export interface SetSearchAction extends AnyAction {
  payload: {
    search: string | { value; [key: string]: any };
  };
}

export interface SetPageAction extends AnyAction {
  payload: {
    page: number;
  };
}
export interface SetPerPageAction extends AnyAction {
  payload: {
    per_page: number;
  };
}
export interface SetSortOrderAction extends AnyAction {
  payload: {
    name: string | null;
    direction: string | null;
  };
}
export interface UpdateExtraFilterAction extends AnyAction {
  payload: { [key: string]: any };
}
export interface SetResetAction extends AnyAction {
  payload: {
    state: State;
  };
}
export type Actions =
  | SetPageAction
  | SetPerPageAction
  | SetSearchAction
  | SetSortOrderAction
  | UpdateExtraFilterAction
  | SetResetAction;
