<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Util\Excel;
use App\Util\Fusion;

use function Src\{channelList, moveUploadedFile, removerArquivosAntigos, shopCartCalculator, validateInformation};

class Quotation
{

    /**
     * Cotação simples
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function simpleQuotation(Request $request, Response $response): Response
    {

        // Pega dados informados na requisição
        $data = $request->getParsedBody();
        $cep = $data['cep'];
        $itens = $data['itens'];
        $channel = $data['canal'];


        // Cria variáveis do carrinho e do produto
        $cartValue = [];
        $products = [];


        try {


            foreach ($itens as $i) {


                // ID item e quantidade informado na requisição
                $idItem = $i['id_item'];
                $qt = intval($i['quantidade']);


                // Busca dados do produto no banco de dados
                $p = new ProductsController;
                $getItems = $p->getItems($idItem);


                if (!isset($getItems)) { // Retorna mensagem de erro se o produto não for encontrado na base


                    $msg = [
                        "Message" => 'Produto não encontrado na base'
                    ];


                    $res = new ResponserController;
                    $response = $res->responseClient($response, $msg, 404);

                    return $response;
                }


                // Pega preço sku CORE e prazo do produto
                $price = floatval($getItems['price']);
                $sku = $getItems['sku_core'];
                $deadline = $getItems['deadline'];


                // Alimenta variável do carrinho
                $shopCart[] = [
                    'price' => $price,
                    'qt' => $qt
                ];


                // Alimenta variável do produto
                $products[] = [
                    "cdItem" => $sku,
                    "sku" => $sku,
                    "qtdItem" => $qt,
                    "vlrItem" => $price
                ];
            }


            // função que calcula valor do carrinho
            $cartValue = shopCartCalculator($shopCart);


            // Cria variável do resultado da cotação
            $result = [];


            // Chama a classe da cotação e do responser
            $qFusion = new Fusion;
            $res = new ResponserController;


            // Verifica se foi selecionado a opção de cotação para todos os canais
            if ($channel == 'todos') {


                // Pega a lista de canais (array)
                $channels = channelList();


                foreach ($channels as $i) {


                    // Faz a cotação na Fusion
                    $quotation = $qFusion->fusion($cep, $i, $products, $cartValue);


                    if ($quotation->msg) {


                        // Organiza o resultado da cotação
                        $prepareReturn = $res->returnQuotation($quotation, $deadline);
                        $response = $res->responseClient($response, $prepareReturn, 400);
                        return $response;
                    }


                    // Função que organiza o resultado das cotações
                    $prepareReturn = $res->returnQuotation($quotation, $deadline);


                    // adiciona informação do canal
                    $prepareReturn['canal'] = $i;


                    // Alimenta a variável com resultado final
                    $result[] = $prepareReturn;
                }
            } else {


                // Faz a cotação no Fusion
                $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);


                if ($quotation->msg) {


                    // Organiza o resultado da cotação
                    $prepareReturn = $res->returnQuotation($quotation, $deadline);
                    $response = $res->responseClient($response, $prepareReturn, 400);
                    return $response;
                }


                // Organiza o resultado da cotação
                $prepareReturn = $res->returnQuotation($quotation, $deadline);


                // Adiciona o canal
                $prepareReturn['canal'] = $channel;


                // Alimenta a variável com resultado final
                $result = [
                    $prepareReturn
                ];
            }


            $response = $res->responseClient($response, $result, 200);
            return $response;
        } catch (\Exception $e) {

            $messageResponse = $e->getMessage();

            $res = new ResponserController;
            $response = $res->responseClient($response, $messageResponse, 400);
            return $response;
        }
    }



    /**
     * Cotação massiva de produtos
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function massiveQuotation(Request $request, Response $response): Response
    {

        // Função que remove arquivos antigos da pasta
        removerArquivosAntigos();


        // Pega o arquivo enviado pelo front
        $directory = 'Uploads/';
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['formProdutos'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {


            // Cria variavel de resultados da cotação
            $resultQuotation = [];


            // Move arquivo da pasta tmp para a pasta uploads
            $filename = moveUploadedFile($directory, $uploadedFile);
            $uploadfile = $directory . $filename;


            // Lê arquivo enviado
            $excel = new Excel($uploadfile);
            $productForQuotation = $excel->readerPlanProducts();


            // Cria variaveis de produtos e carrinho
            $shopCart = [];
            $products = [];


            foreach ($productForQuotation as $i) {


                // Pega quantidade e cep
                $qt = $i['qt'];
                $cep = $i['cep'];


                // Busca dados do produto no banco de dados
                $p = new ProductsController;
                $getItems = $p->getItems($i['idItem']);


                // Se não encontrar o produto no banco, retorna mensagem de erro
                if (!isset($getItems['sku_core'])) {


                    $resultQuotation[] = [
                        'protocolo' => null,
                        'cdMicroServico' => null,
                        'nomeTransportadora' => null,
                        'prazo' => null,
                        'prazoTransit' => null,
                        'prazoExpedicao' => null,
                        'prazoProdutoBseller' => null,
                        'valor' => null,
                        'custo' => null,
                        'erro' => "Produto não encontrado na base de dados"
                    ];
                } else {


                    // Pega preço, sku e prazo do produto
                    $price = floatval($getItems['price']);
                    $sku = $getItems['sku_core'];
                    $deadline = $getItems['deadline'];


                    // Alimenta variável do carrinho
                    $shopCart = [
                        [
                            'price' => $price,
                            'qt' => $qt
                        ]
                    ];


                    // Alimenta variável do produto
                    $products = [
                        [
                            "cdItem" => $sku,
                            "sku" => $sku,
                            "qtdItem" => $qt,
                            "vlrItem" => $price
                        ]
                    ];


                    // Função que calcula valor do carrinho
                    $cartValue = shopCartCalculator($shopCart);


                    // Faz a cotação no Fusion
                    $qFusion = new Fusion;
                    $quotation = $qFusion->fusion($cep, 'Estrela 10', $products, $cartValue);

                    // Alimenta variável com resultado da cotação
                    $resultQuotation[] = [
                        'protocolo' => $quotation->protocolo,
                        'cdMicroServico' => $quotation->modalidades[0]->itens[0]->cdMicroServico ?? null,
                        'nomeTransportadora' => $quotation->modalidades[0]->transportador ?? null,
                        'prazo' => $quotation->modalidades[0]->prazo ?? null,
                        'prazoTransit' => $quotation->modalidades[0]->prazoTransit ?? null,
                        'prazoExpedicao' => $quotation->modalidades[0]->prazoExpedicao ?? null,
                        'prazoProdutoBseller' => $deadline ?? null,
                        'valor' => $quotation->modalidades[0]->valor ?? null,
                        'custo' => $quotation->modalidades[0]->custo ?? null,
                        'erro' => $quotation->msg ?? null
                    ];
                }
            }


            // Grava resultado das cotaçãoes no arquivo
            $excel->writePlanProducts($uploadfile, $resultQuotation);


            // Pega nome do arquivo
            $file = explode('/', $uploadfile);


            // Retorna mensagem pro front/usuário
            $res = new ResponserController;
            $response = $res->responseClient($response, $file[1], 200);
        }

        return $response;
    }


    /**
     * Cotação de produto simulado
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function simulateProduct(Request $request, Response $response): Response
    {

        // Pega dados informados na request
        $data = $request->getParsedBody();


        // Cria variavel
        $res = null;

        try {


            // Pega as informações, caso não informadas retorna mensagem de erro
            $cep = $data['cep'] ?? $res = validateInformation($response, 'cep');
            $channel = $data['canal'] ?? $res = validateInformation($response, 'canal');
            $warehouse = $data['deposito'] ?? $res = validateInformation($response, 'deposito');
            $height = $data['altura'] ?? $res = validateInformation($response, 'altura');
            $length = $data['comprimento'] ?? $res = validateInformation($response, 'comprimento');
            $width = $data['largura'] ?? $res = validateInformation($response, 'largura');
            $weight = $data['peso'] ?? $res = validateInformation($response, 'peso');
            $price = $data['vlrProduto'] ?? $res = validateInformation($response, 'valor do produto');
            $qt = $data['quantidade'] ?? 1;
            $deadline = 0;


            if (!is_null($res)) {
                return $response->withHeader('Content-Type', 'application/json');
            }


            // Identifica o depósito selecionado
            // para determinar o sku que será utilizado na cotação
            if ($warehouse == 'sp') {

                $sku = 'item-teste-sp';
            } elseif ($warehouse == 'sc') {

                $sku = 'item-teste-sc';
            } elseif ($warehouse == 'sc_sp') {

                $sku = 'item-teste';
            }


            // Alimenta variável do carrinho
            $shopCart = [
                [
                    'price' => floatval($price),
                    'qt' => $qt
                ]
            ];


            // Alimenta variável do produto
            $products[] = [
                "cdItem" => $sku,
                "sku" => $sku,
                "qtdItem" => $qt,
                "vlrItem" => floatval($price),
                "comprimento" => $length,
                "largura" => $width,
                "altura" => $height,
                "peso" => $weight
            ];


            // função que calcula valor do carrinho
            $cartValue = shopCartCalculator($shopCart);


            // Cria variável do resultado da cotação
            $result = [];


            // Faz a cotação
            $qFusion = new Fusion;
            $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);


            // Trata o resultado da cotação
            $responseTreatment = new ResponserController;
            $res = $responseTreatment->returnQuotation($quotation, $deadline);


            // Alimenta a variável
            $result[] = $res;

            $response = $responseTreatment->responseClient($response, $result, 200);

            return $response;
        } catch (\Exception $e) {

            $messageResponse = $e->getMessage();

            $res = new ResponserController;
            $response = $res->responseClient($response, $messageResponse, 400);
            return $response;
        }
    }
}
