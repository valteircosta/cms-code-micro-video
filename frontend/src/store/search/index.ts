import * as Typings from "./types";
import { createActions } from "reduxsauce";
/**
 * Using reduxsauce will make types and constant action name 'SET_SEARCH']
 * Not will need  name type 'SetSearchAction' complete also the payload is added automatic
 */
const { Types, Creators } = createActions<
  {
    SET_SEARCH: string;
    SET_PAGE: string;
    SET_PER_PAGE: string;
    SET_SORT_ORDER: string;
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
  }
>({
  setSearch: ["payload"],
  setPage: ["payload"],
  setPerPage: ["payload"],
  SetSortOrder: ["payload"],
});
