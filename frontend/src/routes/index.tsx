import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/List';

// Declaring new interface that extended RouteProps
interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
}

const routes: MyRouteProps[] = [
    {
        label: 'Dashboard',
        name: 'dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        label: 'List of categories',
        name: 'categories.list',
        path: '/categories',
        component: CategoryList,
        exact: true
    }
];
export default routes;