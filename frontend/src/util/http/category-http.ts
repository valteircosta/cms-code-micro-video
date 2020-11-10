import { httpVideo } from "./index";
import HttpResource from "./http-resource";

const categoryHttp = new HttpResource(httpVideo, "categories");

//It help your IDE with autocomplete code, creating a variable and using export default 
export default categoryHttp;
