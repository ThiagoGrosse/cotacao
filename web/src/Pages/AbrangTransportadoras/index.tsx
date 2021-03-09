import React, {useEffect, useState} from 'react';

import api from '../../Services/Api';
import Header from '../../Components/Header/index';
import Footer from '../../Components/Footer/index';
import './Style.css';

export default function AbrangTransp() {

    interface responseAPI {
        id: number,
        starting_track: string,
        final_track: string,
        zone: string,
        state: string,
        charge: string,
        shipping_company_name: string
    }

    const[result, setResult] = useState<responseAPI[]>([]);

    useEffect(() => {
        
        if(result.length === 0 ){

            (async function() {
                const response = await api.get('/transportadoras');

                setResult(response.data)
            }())
        }
    });

    return (
        <div className="wrapper">
            <Header title="Abrangência de transportadoras" />
            <div className="container">
                <div className="tela-tabela">
                    <table className="tableTransp">
                        <thead>
                            <tr>
                                <th>x</th>
                                <th>Cep Inicial</th>
                                <th>Cep Final</th>
                                <th>Região</th>
                                <th>Estado</th>
                                <th>Carga</th>
                                <th>Transportadora</th>
                            </tr>
                        </thead>
                        <tbody>
                        {
                            result.map(( x:responseAPI, i:number ) => {
                                return(
                                <tr key={i}>
                                    <td>{x.id}</td>
                                    <td>{x.starting_track}</td>
                                    <td>{x.final_track}</td>
                                    <td>{x.zone}</td>
                                    <td>{x.state}</td>
                                    <td>{x.charge}</td>
                                    <td>{x.shipping_company_name}</td>
                                </tr>
                                )
                            })
                        }
                        </tbody>
                    </table>
                </div>
            </div>
            <div className="push"></div>
            <Footer />
        </div>
    )
}