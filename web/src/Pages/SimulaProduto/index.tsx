import React, { useState, FormEvent } from 'react';
import Header from '../../Components/Header/index';
import Footer from '../../Components/Footer/index';
import api from '../../Services/Api';
import { cpMask } from '../../Components/Mask/index';
import ModalLoading from '../../Components/Modal/Loading';

export default function SimulaProduto() {

    interface resultCotacao {
        cdMicroServico: string,
        custo: number,
        nomeTransportadora: string,
        prazo: number,
        prazoExpedicao: number,
        prazoProdutoBseller: number,
        prazoTransit: number,
        protocolo: string,
        valor: number
    }


    const [cep, setCep] = useState('');
    const [altura, setAltura] = useState('');
    const [largura, setLargura] = useState('');
    const [comp, setComp] = useState('');
    const [peso, setPeso] = useState('');
    const [quantidade, setQuantidade] = useState(1);
    const [preco, setPreco] = useState('');
    const [deposito, setDeposito] = useState('sc_sp');

    const [modal, setModal] = useState(false);

    const [result, setResult] = useState<resultCotacao[]>([]);
    const [erroResult, setErroResult] = useState('')

    async function enviaCotacao(e:FormEvent) {
        e.preventDefault();
        
        setModal(true)
        
        const data = {
            cep: cep,
            canal: "Estrela 10",
            deposito: deposito,
            altura: parseInt(altura),
            largura: parseInt(largura),
            comprimento: parseInt(comp),
            peso: parseFloat(peso.replace(',','.')),
            vlrProduto: parseFloat(preco.replace(',','.')).toFixed(2),
            quantidade:quantidade
        }

        await api.post('/simulaProduto',data)
            .then((response) => {
                
                setResult(response.data)
                setModal(false)
            })
            .catch((error) => {
                setModal(false)
                setErroResult('Whoops!' + error.response.data || error)
            })

        setModal(false)
    }

    return(
        <div className="wrapper">
            <Header title="Cotação de produto simulado" />
            <div className="container">
                <div className="div-tela">
                    <form onSubmit={enviaCotacao}>

                        {/* CEP */}
                        <div className="input-group mb-3">
                            <span className="input-group-text">CEP</span>
                            <input 
                                type="text" 
                                name="cep" 
                                className="form-control"
                                value={cep}
                                onChange={(e) => {setCep(cpMask(e.target.value))}} 
                                required
                            />
                        </div>

                        {/* Dimensões */}
                        <div className="input-group mb-3">
                            <span className="input-group-text">Dimensões</span>
                            
                            {/* Altura */}
                            <input 
                                type="text"
                                name="altura"
                                className="form-control"
                                placeholder="Altura"
                                value={altura}
                                onChange={(e) => {setAltura(e.target.value)}}
                                required
                            />

                            {/* Largura */}
                            <input
                                type="text"
                                name="largura"
                                className="form-control"
                                placeholder="Largura"
                                value={largura}
                                onChange={(e) => {setLargura(e.target.value)}}
                                required
                            />

                            {/* Comprimento */}
                            <input 
                                type="text"
                                name="comprimento"
                                className="form-control"
                                placeholder="Compr."
                                value={comp}
                                onChange={(e) => {setComp(e.target.value)}}
                                required
                            />

                            {/* Peso */}
                            <input
                                type="text"
                                name="peso"
                                className="form-control"
                                placeholder="Peso"
                                value={peso}
                                onChange={(e) => {setPeso(e.target.value)}}
                                required
                            />
                        </div>

                        <div className="input-group mb-3">
                            <span className="input-group-text">Preço</span>
                            <input 
                                type="text"
                                name="valor"
                                className="form-control"
                                placeholder="R$"
                                value={preco}
                                onChange={(e) => {setPreco(e.target.value)}}
                                required
                            />

                            <span className="input-group-text">Quantidade</span>
                            <input 
                                type="number" 
                                name="quantidade" 
                                className="form-control" 
                                value={quantidade}
                                onChange={(e) => {setQuantidade(e.target.valueAsNumber)}}
                            />
                        </div>

                        <div className="form-group mb-3">
                            <label htmlFor="exampleFormControlSelect1">Depósito</label>
                            <select 
                                className="form-control" 
                                id="exampleFormControlSelect1"
                                value={deposito}
                                onChange={(e) => {setDeposito(e.target.value)}}
                            >
                                <option value="sc_sp">Ambos os CDs</option>
                                <option value="sc">Itajaí - SC</option>
                                <option value="sp">Barueri - SP</option>
                            </select>
                        </div>
                        <div className="buttons">
                            <button type="submit" className="btn btn-success">Calcular</button>
                        </div>
                    </form>
                    <div className="tela-resultado">
                        <pre>
                            {
                                result.length ? JSON.stringify(result, null, 2): ""
                            }
                            {
                                erroResult.length ? JSON.stringify(erroResult, null, 2): ""
                            }
                        </pre>
                    </div>
                </div>
            </div>
            {
                modal && <ModalLoading/>
            }
            <div className="push"></div>
            <Footer />
        </div>
    )
}