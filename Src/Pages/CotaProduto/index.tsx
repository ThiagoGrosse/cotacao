import React, { useState, FormEvent} from 'react';

import Header from '../../Components/Header/index';
import Footer from '../../Components/Footer/index';
import api from '../../Services/Api';
import { cpMask } from '../../Components/Mask/index';
import ModalLoading from '../../Components/Modal/Loading';

export default function CotaProduto() {

    interface resultCotacao {
        cdMicroServico: string,
        custo: string,
        nomeTransportadora: string,
        prazo: number,
        prazoExpedicao: number,
        prazoProdutoBseller: number,
        prazoTransit: number,
        protocolo: string,
        valor: number,
        canal: string
    }

    const [cep, setCep] = useState('');
    const [canal, setCanal] = useState('#');
    const [result, setResult] = useState<resultCotacao[]>([]);
    const [erroResult, setErroResult] = useState('')
    const [table, setTable] = useState(false)
    const [modal, setModal] = useState(false)

    const [addProduto, setAddProduto] = useState([
        {id_item:'' , quantidade: 1}
    ])

    function addNewProduto() {
        setAddProduto([
            ...addProduto,
            { id_item:'', quantidade:1 }
        ])
    }

    function setValoresProdutos (position:number, field:string, value:string) {
        const updateProduto = addProduto.map((x,y) => {
            if(y === position) {
                return {...x, [field]:value }
            }

            return x;
        })

        setAddProduto(updateProduto);
    }

    async function enviaCotacao(e:FormEvent){
        e.preventDefault();

        setModal(true)
        setResult([])
        setErroResult('')
        setTable(false)

        const body = {
            cep: cep,
            canal: canal,
            itens: addProduto
        }

        await api.post('/cotacao', body)
            .then((response) => {
                              
                setResult(response.data)
                setTable(true)
                setModal(false)
            })
            .catch((error) => {
                setModal(false)
                setErroResult('Whoops!' + error.response.data || error)
            })
    }

    return(
        <div className="wrapper">
            <Header title="Cotação de produtos" />
            <div className="container">
                <div className="div-tela">
                    <form onSubmit={enviaCotacao}>
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
                        <div className="form-group mb-3">
                            <label htmlFor="exampleFormControlSelect1">Canal</label>
                            <select 
                                className="form-control" 
                                id="exampleFormControlSelect1"
                                value={canal}
                                onChange={(e) => {setCanal(e.target.value)}}
                            >
                                <option value="#" disabled>Selecione</option>
                                <option value="estrela 10">Estrela 10</option>
                                <option value="b2w">B2W</option>
                                <option value="carrefour">Carrefour</option>
                                <option value="homolog">Homolog</option>
                                <option value="magalu">Magazine Luiza</option>
                                <option value="mercado l">Mercado Livre</option>
                                <option value="todos">Todos os canais</option>
                            </select>
                        </div>
                        {addProduto.map((x,i) => {
                            return (
                                <div className="input-group mb-3" key={i}>
                                    <span className="input-group-text">Produto</span>
                                    
                                    <input
                                        type="text"
                                        name="id_item"
                                        className="form-control"
                                        placeholder="ID produto"
                                        value={x.id_item}
                                        onChange={e => setValoresProdutos(i,'id_item',e.target.value) }
                                        required
                                    />
                                    
                                    <input
                                        type="number"
                                        name="quantidade"
                                        className="form-control"
                                        value={x.quantidade}
                                        onChange={e => setValoresProdutos(i,'quantidade',e.target.value) }
                                    />

                                </div>
                            )
                        })}

                        <div className="buttons">
                            <button type="button" onClick={addNewProduto} className="btn btn-primary">Adicionar produto</button>
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

                    {
                        table &&
                            <table className="table table-striped table-dark table-request">
                                <thead>
                                    <tr>
                                        <th>Canal</th>
                                        <th>Protocolo</th>
                                        <th>MicroServico</th>
                                        <th>Transportadora</th>
                                        <th>Prazo</th>
                                        <th>Prazo Expedição</th>
                                        <th>Prazo Produto</th>
                                        <th>Prazo Transito</th>
                                        <th>Valor</th>
                                        <th>Custo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {
                                        result.map((x,y) => {
                                            return (
                                                <tr key={y}>
                                                    <td>{x.canal}</td>
                                                    <td>{x.protocolo}</td>
                                                    <td>{x.cdMicroServico}</td>
                                                    <td>{x.nomeTransportadora}</td>
                                                    <td>{x.prazo}</td>
                                                    <td>{x.prazoExpedicao}</td>
                                                    <td>{x.prazoProdutoBseller}</td>
                                                    <td>{x.prazoTransit}</td>
                                                    <td>R$ {x.valor}</td>
                                                    <td>R$ {x.custo}</td>
                                                </tr>
                                            )
                                        })
                                    }
                                </tbody>
                            </table>
                                    
                    }
            </div>
            {
                modal && <ModalLoading/>
            }
            <div className="push"></div>
            <Footer />
        </div>
    )
}
