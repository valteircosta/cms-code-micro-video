import { RouteProps } from 'react-router-dom';

// Declaring new interface that extended RouteProps
interface MyRouteProps extends RouteProps {
    label: string;
}

const routes: MyRouteProps[] = [
    {
        label: 'Dashboard',
        path: '/',
        component: '',
        exact: true
    },
    {
        label: 'Lista de categorias',
        path: '/categories',
        component: '',
        exact: true
    }
];
export default routes;