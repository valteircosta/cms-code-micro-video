import { Box } from '@material-ui/core';
import React from 'react';
import { Navbar } from './components/Navbar';
import { Page } from './components/Page';

const App: React.FC = () => {
  return (
    <React.Fragment>
      <Navbar />
      <Box paddingTop={'70px'} >
        <Page title={'Categorias'} />
      </Box >
    </React.Fragment >
  );
}

export default App;
