

import { AppBar, Toolbar } from '@material-ui/core';
import * as React from 'react';

export const Navbar: React.FC = () => {
    return (
        <div>
            <AppBar>
                <Toolbar>
                    Navbar name
                </Toolbar>
            </AppBar>
        </div>
    );
};