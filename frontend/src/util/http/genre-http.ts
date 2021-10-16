import { httpVideo } from "./index";
import HttpResource from "./http-resource";

const genreHttp = new HttpResource(httpVideo, "genres");

//It help your IDE with autocomplete code, creating a variable and using export default
export default genreHttp;
