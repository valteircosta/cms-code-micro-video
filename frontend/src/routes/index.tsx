import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';
import CategoryForm from '../pages/category/PageForm';
import GenreList from '../pages/genre/PageList';
import GenreForm from '../pages/genre/PageForm';
import CastMemberList from '../pages/cast-member/PageList';
import CastMemberForm from '../pages/cast-member/PageForm';
import VideoList from '../pages/video/PageList';
import VideoForm from '../pages/video/PageForm';


// Declaring new interface that extended RouteProps
export interface MyRouteProps extends RouteProps {

    name: string;
    label: string;
}

const routes: MyRouteProps[] = [
    {
        label: "Dashboard",
        name: "dashboard",
        path: "/",
        component: Dashboard,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Listar categorias",
        /* cSpell:enable */
        name: "categories.list",
        path: "/categories",
        component: CategoryList,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Criar categoria",
        /* cSpell:enable */
        name: "categories.create",
        path: "/categories/create",
        component: CategoryForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Editar categorias",
        /* cSpell:enable */
        name: "categories.edit",
        path: "/categories/:id/edit",
        component: CategoryForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Listar membros de elenco",
        /* cSpell:enable */
        name: "cast-members.list",
        path: "/cast-members",
        component: CastMemberList,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Criar membro de elenco",
        /* cSpell:enable */
        name: "cast-members.create",
        path: "/cast-members/create",
        component: CastMemberForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Edit membro de elenco",
        /* cSpell:enable */
        name: "cast-members.edit",
        path: "/cast-members/:id/edit",
        component: CastMemberForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Listar gêneros",
        /* cSpell:enable */
        name: "genres.list",
        path: "/genres",
        component: GenreList,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Criar gênero",
        /* cSpell:enable */
        name: "genres.create",
        path: "/genres/create",
        component: GenreForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Edit gênero",
        /* cSpell:enable */
        name: "genres.edit",
        path: "/genres/:id/edit",
        component: GenreForm,
        exact: true
    },
  {
        /* cSpell:disable */
        label: "Listar vídeos",
        /* cSpell:enable */
        name: "videos.list",
        path: "/videos",
        component: VideoList,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Criar vídeo",
        /* cSpell:enable */
        name: "videos.create",
        path: "/videos/create",
        component: VideoForm,
        exact: true
    },
    {
        /* cSpell:disable */
        label: "Editar vídeo",
        /* cSpell:enable */
        name: "videos.edit",
        path: "/videos/:id/edit",
        component: VideoForm,
        exact: true
    },
   
];
export default routes;