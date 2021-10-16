import { httpVideo } from "./index";
import HttpResource from "./http-resource";

const videoHttp = new HttpResource(httpVideo, "videos");

//It help your IDE with autocomplete code, creating a variable and using export default
export default videoHttp;
