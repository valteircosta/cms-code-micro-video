import React from 'react';
import { makeStyles, Theme, createStyles } from '@material-ui/core/styles';
import Link, { LinkProps } from '@material-ui/core/Link';
import Typography from '@material-ui/core/Typography';
import MuiBreadcrumbs from '@material-ui/core/Breadcrumbs';
import { Route } from 'react-router';
import { Link as RouterLink } from 'react-router-dom';
import { Location } from 'history';
import routes from '../routes';
import RouteParser from 'route-parser';

const breadcrumbNameMap: { [key: string]: string } = {};
routes.forEach(route => breadcrumbNameMap[route.path as string] = route.label);

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    root: {
      display: 'flex',
      flexDirection: 'column',
      width: 360,
    },
    lists: {
      backgroundColor: theme.palette.background.paper,
      marginTop: theme.spacing(1),
    },
    nested: {
      paddingLeft: theme.spacing(4),
    },
  }),
);

interface LinkRouterProps extends LinkProps {
  to: string;
  replace?: boolean;
}

const LinkRouter = (props: LinkRouterProps) => <Link {...props} component={RouterLink as any} />;

export default function Breadcrumbs() {
  const classes = useStyles();

  function makeBreadcrumb(location:Location) {
  
    const pathnames = location.pathname.split('/').filter((x) => x);
    pathnames.unshift('/'); // Make array type ['/','categories','create']
    console.log(pathnames, location.pathname);

    return (
      <MuiBreadcrumbs aria-label="breadcrumb">
        
        {
          pathnames.map((value, index) => { // array [/,categories,create, others]
            const last = index === pathnames.length - 1;
            // First value is '//categories/create/others then apply replace method to fix in /categories/create/others
            const to = `${pathnames.slice(0, index + 1).join('/').replace('//','/')}`;
            console.log('to :>> ', to);
            const route = Object
            .keys(breadcrumbNameMap)
            .find(path => new RouteParser(path).match(to));
            if (route === undefined) {
              return false
            }
            return last ? (
              <Typography color="textPrimary" key={to}>
                {breadcrumbNameMap[route]}
              </Typography>
            ) : (
                <LinkRouter color="inherit" to={to} key={to}>
                  {breadcrumbNameMap[route]}
                </LinkRouter>
              );
          })

        }
      </MuiBreadcrumbs>
    );
  }
  return (
    <div className={classes.root}>
      <Route>
        {
          /**  Executing Destructuring Object  method,  getting the location property through Location Object  */
         ({location}:{location:Location}) => makeBreadcrumb(location)
        }
      </Route>
    </div>
  );
}