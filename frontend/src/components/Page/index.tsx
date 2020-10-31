// @flow 
import { Container, makeStyles, Typography } from '@material-ui/core';
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
                <Typography className={classes.title} >
                    {props.title}
                </Typography>
            </Container>
        </div>
    );
};