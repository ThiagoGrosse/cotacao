import React from 'react';
import { BrowserRouter, Route }from 'react-router-dom';
import CotaProduto from './Pages/CotaProduto/index';
import SimulaProduto from './Pages/SimulaProduto/index';
import CotacaoMassiva from './Pages/CotacoesMassivas/index';
import AbrangTransp from './Pages/AbrangTransportadoras/index';

export default function Routes() {
    return(
        <BrowserRouter>
            <Route path='/' exact component={CotaProduto} />
            <Route path='/simula-produto' component={SimulaProduto} />
            <Route path='/cotacoes-massivas' component={CotacaoMassiva} />
            <Route path='/abrangencia-transportadoras' component={AbrangTransp} />
        </BrowserRouter>
    );
}
