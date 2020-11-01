import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/List';

// Declaring new interface that extended RouteProps
interface MyRouteProps extends RouteProps {
    label: string;
}

const routes: MyRouteProps[] = [
    {
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        label: 'Listagem de categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    }
];
export default routes;