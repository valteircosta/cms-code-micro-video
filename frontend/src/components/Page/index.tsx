// @flow 
import { Box, Container, makeStyles, Typography } from '@material-ui/core';
import * as React from 'react';

const useStyles = makeStyles({
    title: { color: '#999999' }
});

type PageProps = {
    title: String;
};
export const Page: React.FC<PageProps> = (props) => {
    const classes = useStyles();
    return (
        <div>
            <Container>
                <Typography className={classes.title} component='h1' variant='h5' >
                    {props.title}
                </Typography>
                <Box paddingTop={1}>
                    {props.children}
                </Box>
            </Container>
        </div>
    );
};