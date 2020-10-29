

import * as React from 'react';
import { AppBar, Button, makeStyles, Toolbar, Typography } from '@material-ui/core';
import logo from '../../static/img/logo.png';

//Make object with styles
const useStyles = makeStyles({
    toolbar: {
        backgroundColor: '#000000'
    }
});

export const Navbar: React.FC = () => {
    //Execute function return object styles
    const classes = useStyles();
    console.log(classes);
    return (
        <div>
            <AppBar>
                <Toolbar className={classes.toolbar}>
                    <Typography>

                        <img src={logo} alt="CodeFlix" />
                    </Typography>
                    <Button color="inherit">Login</Button>
                </Toolbar>
            </AppBar>
        </div>
    );
};