// @flow 
import * as React from 'react';
import { IconButton, makeStyles, Tooltip } from '@material-ui/core';
import ClearAllIcon from '@material-ui/icons/ClearAll';

const useStyles = makeStyles(theme => ({
    iconButton: (theme as any).overrides.MUIDataTableToolbar.icon
}));

export const FilterResetButton = (props) => {
    const classes = useStyles();
    return (
        <Tooltip title={'Limpar busca'}>
            <IconButton className={classes.iconButton}>
                <ClearAllIcon />
            </IconButton>
        </Tooltip>
    );
};