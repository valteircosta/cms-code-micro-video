// @flow 
import * as React from 'react';
import { IconButton, Menu as MuiMenu, MenuItem } from '@material-ui/core';
import MenuIcon from "@material-ui/icons/Menu";
import routes, { MyRouteProps } from '../../routes';
import { Link } from 'react-router-dom';


/* cSpell:disable */
const listRoutes = {
    'dashboard': 'Dasboard',
    'categories.list': 'Categorias',
    'cast-members.list': 'Membros do Elenco',
    'genres.list': 'Gêneros',
    'videos.list': 'Vídeos',
};
const menuRoutes = routes.filter(route => Object.keys(listRoutes).includes(route.name));
/* cSpell:enable */

export const Menu = () => {

    /**
    * I Make first Hook with React, his define initial value for open property of the menu and contain
    */
    const [anchorEl, setAnchorEl] = React.useState(null);
    const open = Boolean(anchorEl);
    // Event handler for IconButton
    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);


    return (
        <React.Fragment>

            { /* Note..: When use two or plus line, correctly alignment is line over line in code  */}

            <IconButton
                color="inherit"
                edge="start"
                aria-label="open drawer"
                /* cSpell:disable */
                aria-controls="menu-appbar"
                /* cSpell:enable */
                aria-haspopup="true"
                onClick={handleOpen} //Event make above
            >
                <MenuIcon />
            </IconButton>
            <MuiMenu
                /* cSpell:disable */
                id="menu-appbar"
                /* cSpell:enable */
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose} //Event make for close menu
                // Change menu position on the form
                anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
                transformOrigin={{ vertical: 'top', horizontal: 'center' }}
                getContentAnchorEl={null}
            >
                {
                    Object.keys(listRoutes).map(
                        (routeName, key) => {
                            // as MyRouteProps is a typecast of the typescript, "as" is operator
                            const route = menuRoutes.find(route => route.name === routeName) as MyRouteProps;
                            return (
                                <MenuItem
                                    key={key}
                                    component={Link}
                                    to={route.path as string}
                                    onClick={handleClose}
                                >
                                    {listRoutes[routeName]}
                                </MenuItem>
                            )
                        }
                    )
                }
            </MuiMenu>
        </React.Fragment>
    );
};