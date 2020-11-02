import React from 'react';
import { Box } from '@material-ui/core';
import { Navbar } from './components/Navbar';
import { BrowserRouter } from 'react-router-dom';
import AppRouter from './routes/AppRouter';
import Breadcrumbs from './components/Breadcrumbs';

const App: React.FC = () => {
  return (
    <React.Fragment>
      <BrowserRouter>
      <Navbar/>
        <Box paddingTop={'70px'} >
          <Breadcrumbs/>
          <AppRouter/>
        </Box >
      </BrowserRouter>
    </React.Fragment >
  );
}

export default App;
