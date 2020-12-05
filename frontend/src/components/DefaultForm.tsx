// @flow 
import * as React from 'react';
import { Grid, GridProps, makeStyles } from '@material-ui/core';

const useStyles = makeStyles(theme => ({
    gridItem: {
        padding: theme.spacing(1, 0)
    },
}));

/** For keep properties component original we are using extends it  */
interface DefaultFormProps extends React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> {
    GridContainerProps?: GridProps;
    GridItemProps?: GridProps;
}
/** For keep properties and auto complete code through IDE was used type definition  */
export const DefaultForm: React.FC<DefaultFormProps> = (props) => {

    const classes = useStyles();
    const { GridContainerProps, GridItemProps, ...other } = props;
    
    return (

        /** Transfer properties using spread operator */
        <form {...other}>
            <Grid container {...GridContainerProps}>
                <Grid className={classes.gridItem} item {...GridItemProps}>
                    {props.children}
                </Grid>
            </Grid>
        </form>

    );
};