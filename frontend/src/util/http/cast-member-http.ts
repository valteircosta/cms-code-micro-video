import { httpVideo } from "./index";
import HttpResource from "./http-resource";

const castMemberHttp = new HttpResource(httpVideo, "cast_members");

//It help your IDE with autocomplete code, creating a variable and using export default
export default castMemberHttp;
