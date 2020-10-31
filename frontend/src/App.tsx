import React from 'react';
import { Navbar } from './components/Navbar';
import { Page } from './components/Page';

const App: React.FC = () => {
  return (
    <React.Fragment>
      <Navbar />
      <Page title={'Categorias'} />
    </React.Fragment>
  );
}

export default App;
