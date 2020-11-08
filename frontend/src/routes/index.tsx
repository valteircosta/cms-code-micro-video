import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';
import GenreList from '../pages/genre/PageList';
import CastMemberList from '../pages/cast-member/PageList';

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
        component: CategoryList,
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
        component: CastMemberList,
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
        component: GenreList,
        exact: true
    },
];
export default routes;