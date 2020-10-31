// @flow 
import { Container, Typography } from '@material-ui/core';
import * as React from 'react';
type PageProps = {
    title: String;
};
export const Page: React.FC<PageProps> = (props) => {
    return (
        <div>
            <Container>
                <Typography>
                    {props.title}
                </Typography>
            </Container>
        </div>
    );
};