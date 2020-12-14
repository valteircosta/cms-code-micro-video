import { SetSearchAction } from "./types";
import { createActions } from "reduxsauce";
/**
 * Using reduxsauce will make types and constant action name 'SET_SEARCH']
 * Not will need  name type 'SetSearchAction' complete also the payload is added automatic
 */
const { Types, Creators } = createActions({
  setSearch: ["payload"],
  setPage: ["payload"],
  setPerPage: ["payload"],
  SetSortOrder: ["payload"],
});
