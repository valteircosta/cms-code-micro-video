

import * as React from 'react';
import { AppBar, Button, makeStyles, Toolbar, Typography } from '@material-ui/core';
import logo from '../../static/img/logo.png';

//Make object with styles
const useStyles = makeStyles({
    toolbar: {
        backgroundColor: '#000000'
    },
    title: {
        flexGrow: 1,
        textAlign: 'center'
    }
});

export const Navbar: React.FC = () => {
    //Execute function return object styles
    const classes = useStyles();
    return (
        <div>
            <AppBar>
                <Toolbar className={classes.toolbar}>
                    <Typography className={classes.title}>

                        <img src={logo} alt="CodeFlix" />
                    </Typography>
                    <Button color="inherit">Login</Button>
                </Toolbar>
            </AppBar>
        </div>
    );
};