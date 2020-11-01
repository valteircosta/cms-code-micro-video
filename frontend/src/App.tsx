import { Box } from '@material-ui/core';
import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import { Navbar } from './components/Navbar';
import AppRouter from './routes/AppRouter';

const App: React.FC = () => {
  return (
    <React.Fragment>
      <Navbar />
      <BrowserRouter>
        <Box paddingTop={'70px'} >
          <AppRouter />
        </Box >
      </BrowserRouter>
    </React.Fragment >
  );
}

export default App;
