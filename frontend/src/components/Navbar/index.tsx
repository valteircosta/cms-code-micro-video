import * as React from "react";
import {
    AppBar,
    Button,
    IconButton,
    makeStyles,
    Menu,
    MenuItem,
    Theme,
    Toolbar,
    Typography,
} from "@material-ui/core";
import logo from "../../static/img/logo.png";
import MenuIcon from "@material-ui/icons/Menu";

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
    //Execute function return object styles
    const classes = useStyles();
    return (
        <div>
            <AppBar>
                <Toolbar className={classes.toolbar}>
                    {/* Note..: When use two or plus line, correctly alignment is line over line in code  */}
                    <IconButton
                        color="inherit"
                        edge="start"
                        aria-label="open drawer"
                        aria-controls="menu-appbar"
                        aria-haspopup="true"
                    >
                        <MenuIcon>
                        </MenuIcon>
                    </IconButton>
                    <Menu
                        id="menu-appbar"
                        open={true}
                    >
                        <MenuItem>
                            Categorias
                        </MenuItem>

                    </Menu>
                    <Typography className={classes.title}>
                        <img src={logo} alt="CodeFlix" />
                    </Typography>
                    <Button color="inherit">Login</Button>
                </Toolbar>
            </AppBar>
        </div>
    );
}