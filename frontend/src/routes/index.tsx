import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';

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
    
];
export default routes;