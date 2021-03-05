import React, { FormEvent, useState } from 'react';
import $ from 'jquery'

import Header from '../../Components/Header/index';
import Footer from '../../Components/Footer/index';
import ModalLoading from '../../Components/Modal/Loading';

export default function CotacaoMassiva() {

    const [filePedidos, setFilePedidos] = useState('');
    const [fileNamePedidos, setFileNamePedidos] = useState('');
    
    const [fileProdutos, setFileProdutos] = useState('');
    const [fileNameProdutos, setFileNameProdutos] = useState('');

    const [table, setTable] = useState(false);

    const [erroAPI, setErroAPI] = useState('');
    const [resultApi, setResultApi] = useState('');

    const [fileDownload, setFileDownload] = useState('');

    const onChange = (e: any) => {
        
        if(e.target.files.length !== 0 ){
            setFilePedidos(e.target.files[0]);
            setFileNamePedidos(e.target.files[0].name);
        }else{
            setFilePedidos('');
            setFileNamePedidos('');

            setErroAPI("Nenhum arquivo selecionado");
        }
    };

    const onChangeProdutos = (e: any) => {

        if(e.target.files.length !== 0 ){
            setFileProdutos(e.target.files[0]);
            setFileNameProdutos(e.target.files[0].name);
        }else{

            setFileProdutos('');
            setFileNameProdutos('');
            setErroAPI("Nenhum arquivo selecionado");
        }
    };
    
    // === submit pedidos
    function onSubmitPedidos(e:FormEvent) {
        e.preventDefault();

        setErroAPI('');
        setResultApi('');
        setTable(true)

        const obj = new FormData();
        obj.append('formPedidos', filePedidos);

        var path = fileNamePedidos;
        var ext = path.split('.').pop();

        if (ext === 'xlsx'){

            $.ajax({
                url:'http://localhost/cotacao-2021/api/v1/massivo/cotacaoMassivaPedidos',
                type: 'POST',
                data: obj,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    
                    const file = data

                    setResultApi("Download começará em breve!")
                    setTable(false)

                    const urlFile = 'http://localhost/cotacao-2021/Uploads/'+file

                    setFileDownload(urlFile)
                },
                error: function(error){
                    setErroAPI(error.responseJSON)
                    setTable(false)
                }
            })
        } else {
            setTable(false)
        }
    }

    // === submit produtos
    function onSubmitProdutos(e:FormEvent) {
        e.preventDefault();

        setErroAPI('');
        setResultApi('');
        setTable(true)

        const produtos = new FormData();
        produtos.append('formProdutos', fileProdutos)

        var path = fileNameProdutos;
        var ext = path.split('.').pop();

        if (ext === 'xlsx'){
            
            $.ajax({
                url:'http://localhost/cotacao-2021/api/v1/massivo/cotacaoMassiva',
                type: 'POST',
                data: produtos,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    
                    const file = data

                    setResultApi("Download começará em breve!")
                    setTable(false)

                    const urlFile = 'http://localhost/cotacao-2021/Uploads/'+file

                    setFileDownload(urlFile)
                },
                error: function(error){

                    setTable(false)
                    console.log(error.responseJSON)
                }
            })
        } else {

            setTable(false)
        }
    }

    return (
        <div className="wrapper">
            <Header title="Cotações massivas" />
            <div className="container">
                <div className="div-tela">
                    <div className="forms">

                        {/* FORM PEDIDOS */}
                        <p>Pedidos</p>
                        <form onSubmit={onSubmitPedidos}>
                            <div className="input-group mb-3">
                                <input 
                                    type="file"
                                    name="formPedidos"
                                    className="form-control"
                                    onChange={onChange} 
                                    required
                                />
                                <button className="btn btn-primary">Enviar</button>
                            </div>
                        </form>

                        {/* FORM PRODUTOS */}
                        <p>Produtos</p>
                        <form onSubmit={onSubmitProdutos}>
                            <div className="input-group mb-3">
                                <input 
                                    type="file"
                                    name="formProdutos"
                                    className="form-control"
                                    onChange={onChangeProdutos} 
                                    required
                                />
                                <button className="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                    <div className="tela-resultado result-massivo">

                        {
                            erroAPI.length ? erroAPI: ""
                        }
                        {
                            resultApi.length ? <div className="text-download"><span>Clique <a href={fileDownload} download>aqui</a> para efetuar o download</span></div>: ''
                        }

                    </div>
                    
                    <ul>
                        <li><a href="/modelo_pedidos.xlsx" target="_blank" download>Download planilha modelo - Pedidos</a></li>
                        <li><a href="/modelo_produtos.xlsx" target="_blank" download>Download planilha modelo - Produtos</a></li>
                    </ul>
                </div>
            </div>
            {
                table && <ModalLoading />
            }
            <div className="push"></div>
            <Footer />
        </div>
    )
}