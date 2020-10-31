import * as React from "react";
import {
    AppBar,
    Button,
    makeStyles,
    Theme,
    Toolbar,
    Typography,
} from "@material-ui/core";
import logo from "../../static/img/logo.png";
import { Menu } from "./Menu";

//Make object with styles using Theme
const useStyles = makeStyles((theme: Theme) => ({
    toolbar: {
        backgroundColor: "#000000",
    },
    title: {
        flexGrow: 1,
        textAlign: "center",
    },
    logo: {
        width: 100,
        [theme.breakpoints.up('sm')]: {
            width: 170
        }
    }
}));
export const Navbar: React.FC = () => {
    // Execute function return object styles
    const classes = useStyles();
    return (
        <div>
            <AppBar>
                <Toolbar className={classes.toolbar}>
                    <Menu />
                    <Typography className={classes.title} >
                        <img src={logo} alt="CodeFlix" />
                    </Typography>
                    <Button color="inherit">Login</Button>
                </Toolbar>
            </AppBar>
        </div>
    );
}