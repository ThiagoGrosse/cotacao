<?php

namespace App\Controllers;

use App\Util\Core;
use App\Util\Excel;
use App\Util\Fusion;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function Src\{deParaChannel, moveUploadedFile, removerArquivosAntigos, shopCartCalculator};

class OrderQuotation
{


    /**
     * Cotação massiva dos pedidos
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function orderListQuote(Request $request, Response $response): Response
    {

        // função que remove arquivos antigos da pasta
        removerArquivosAntigos();


        // Pega o arquivo enviado pelo front
        $directory = 'Uploads/';
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['formPedidos'];


        // Verifica se deu erro no carregamento do arquivo
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

            // Cria variavel de resultados da cotação
            $resultQuotation = [];

            // Move arquivo da pasta tmp para a pasta uploads
            $filename = moveUploadedFile($directory, $uploadedFile);
            $uploadfile = $directory . $filename;


            // === Lê arquivo enviado
            $excel = new Excel($uploadfile);
            $orders  = $excel->readerPlanOrders();


            foreach ($orders as $i) {

                $idPedido = $i['pedido'];


                // === Chama class Core e faz consulta dos pedidos na api do CORE
                $core = new Core;
                $data = $core->getOrderByCore($idPedido);


                if (empty($data->DeliveryPostalCode)) { // Se não retornar o cep no request da api CORE


                    $resultQuotation[] = [
                        'protocolo' => '',
                        'cdMicroServico' => '',
                        'nomeTransportadora' => '',
                        'prazo' => '',
                        'prazoTransit' => '',
                        'prazoExpedicao' => '',
                        'prazoProdutoBseller' => '',
                        'valor' => '',
                        'custo' => '',
                        'erro' => 'Não foi possível obter dados do pedido'
                    ];

                    continue;
                }


                // Pega cep e canal do pedido
                $cep = $data->DeliveryPostalCode;
                $channelOriginOrder = $data->MarketPlaceBrand;


                /**
                 * Função que converte o canal informado pela api do CORE para formato aceito pelo Fusion
                 * Canal Amazon que não possui cotação é alterado para Estrela 10
                 */
                $channel = deParaChannel($channelOriginOrder);


                // Se nenhum canal retornar da função, retorna informação de canal não encontrado
                if ($channel == 'Error') {

                    $resultQuotation[] = [
                        'protocolo' => '',
                        'cdMicroServico' => '',
                        'nomeTransportadora' => '',
                        'prazo' => '',
                        'prazoTransit' => '',
                        'prazoExpedicao' => '',
                        'prazoProdutoBseller' => '',
                        'valor' => '',
                        'custo' => '',
                        'erro' => 'Canal ' . $channelOriginOrder . ' não identificado'
                    ];

                    continue;
                }

                // Cria variaveis de produtos e carrinho
                $shopCart = [];
                $products = [];


                foreach ($data->Items as $i) {


                    // Pega sku CORE, quantidade e preço do produto do pedido
                    $sku = $i->ProductID . '-' . $i->SkuID;
                    $qt = $i->Qty;
                    $price = $i->Price;


                    // Alimenta a variavel do carrinho
                    $shopCart[] = [
                        'price' => $price,
                        'qt' => $qt
                    ];


                    // Alimenta a variavel de produto
                    $products[] = [
                        "cdItem" => $sku,
                        "sku" => $sku,
                        "qtdItem" => $qt,
                        "vlrItem" => $price
                    ];
                }


                // Chama função que calcula valores do carrinho
                $cartValue = shopCartCalculator($shopCart);


                // Faz a cotação no Fusion
                $qFusion = new Fusion;
                $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);


                // Alimenta variavel de resultado da cotação
                $resultQuotation[] = [
                    'protocolo' => $quotation->protocolo ?? null,
                    'cdMicroServico' => $quotation->modalidades[0]->itens[0]->cdMicroServico ?? null,
                    'nomeTransportadora' => $quotation->modalidades[0]->transportador ?? null,
                    'prazo' => $quotation->modalidades[0]->prazo ?? null,
                    'prazoTransit' => $quotation->modalidades[0]->prazoTransit ?? null,
                    'prazoExpedicao' => $quotation->modalidades[0]->prazoExpedicao ?? null,
                    'valor' => $quotation->modalidades[0]->valor ?? null,
                    'custo' => $quotation->modalidades[0]->custo ?? null,
                    'erro' => $quotation->msg ?? null
                ];
            }


            // Função que grava resultados das cotações na planilha
            $excel->writePlanOrders($uploadfile, $resultQuotation);;


            // Pega nome do arquivo
            $file = explode("/", $uploadfile);


            // Retorna nome do arquivo
            $res = new ResponserController;
            $response = $res->responseClient($response, $file[1], 200);
        }

        return $response;
    }
}
