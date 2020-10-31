// @flow 
import * as React from 'react';
import { IconButton, Menu as MuiMenu, MenuItem } from '@material-ui/core';
import MenuIcon from "@material-ui/icons/Menu";


export const Menu = () => {
    /**
    * I Make first Hook with React, his define inicial value for open property of the menu and contain
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
                aria-controls="menu-appbar"
                aria-haspopup="true"
                onClick={handleOpen} //Event make above
            >
                <MenuIcon>
                </MenuIcon>
            </IconButton>
            <MuiMenu
                id="menu-appbar"
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose} //Event make for close menu
                // Change menu position on the form
                anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
                transformOrigin={{ vertical: 'top', horizontal: 'center' }}
                getContentAnchorEl={null}
            >
                <MenuItem onClick={handleClose} >
                    Categorias
                        </MenuItem>
            </MuiMenu>
        </React.Fragment>
    );
};