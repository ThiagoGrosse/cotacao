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
     * Monta o json que será enviado ao Fusion
     * Chama a class que vai fazer a cotação na Fusion
     * Monta o json que será exibido ao client
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function simpleQuotation(Request $request, Response $response): Response
    {

        $data = $request->getParsedBody();
        $cep = $data['cep'];
        $itens = $data['itens'];
        $channel = $data['canal'];

        $cartValue = [];
        $products = [];

        try {

            foreach ($itens as $i) {

                $idItem = $i['id_item'];
                $qt = intval($i['quantidade']);

                $p = new ProductsController;
                $getItems = $p->getItems($idItem);

                if (!isset($getItems)) {

                    $res = new ResponserController;
                    $response = $res->responseClient($response, 'Produto não encontrado na base', 404, "Error");

                    return $response;
                }

                $price = floatval($getItems['price']);
                $sku = $getItems['sku_core'];
                $deadline = $getItems['deadline'];

                $shopCart[] = [
                    'price' => $price,
                    'qt' => $qt
                ];

                $products[] = [
                    "cdItem" => $sku,
                    "sku" => $sku,
                    "qtdItem" => $qt,
                    "vlrItem" => $price
                ];
            }

            $cartValue = shopCartCalculator($shopCart);

            $result = [];

            $qFusion = new Fusion;
            $res = new ResponserController;

            if ($channel == 'todos') {

                $channels = channelList();

                foreach ($channels as $i) {

                    $quotation = $qFusion->fusion($cep, $i, $products, $cartValue);

                    $prepareReturn = $res->returnQuotation($quotation, $deadline);

                    if (!empty($prepareReturn['Type'])) {

                        $response = $res->responseClient($response, $prepareReturn['Message'], 400);

                        return $response;
                    }

                    $prepareReturn['canal'] = $i;

                    $result[] = $prepareReturn;
                }
            } else {

                $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);

                $prepareReturn = $res->returnQuotation($quotation, $deadline);
                $prepareReturn['canal'] = $channel;

                if (!empty($prepareReturn['Type'])) {

                    $response = $res->responseClient($response, $prepareReturn['Message'], 400);

                    return $response;
                }

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

        removerArquivosAntigos();

        $directory = 'Uploads/';

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['formProdutos'];

        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);

            $uploadfile = $directory . $filename;

            $excel = new Excel($uploadfile);
            $productForQuotation = $excel->readerPlanProducts();

            $resultQuotation = [];

            foreach ($productForQuotation as $i) {

                $qt = intval($i['qt']);
                $cep = $i['cep'];
                $channel = $i['channel'];

                $p = new ProductsController;
                $getItems = $p->getItems($i['idItem']);

                if (!isset($getItems)) {

                    $res = new ResponserController;
                    $res->responseClient($response, 'Produto não encontrado na base', 400, "Error");
                }

                $price = floatval($getItems['price']);
                $sku = $getItems['sku_core'];
                $deadline = $getItems['deadline'];

                $shopCart = [
                    [
                        'price' => $price,
                        'qt' => $qt
                    ]
                ];

                $products = [
                    [
                        "cdItem" => $sku,
                        "sku" => $sku,
                        "qtdItem" => $qt,
                        "vlrItem" => $price
                    ]
                ];

                $cartValue = shopCartCalculator($shopCart);

                $qFusion = new Fusion;
                $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);

                $resultQuotation[] = [
                    'protocolo' => $quotation->protocolo ?? null,
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

            $excel->writePlanProducts($uploadfile, $resultQuotation);

            $file = explode('/', $uploadfile);

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
        $data = $request->getParsedBody();

        $res = null;

        try {

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

            if ($warehouse == 'sp') {

                $sku = 'item-teste-sp';
            } elseif ($warehouse == 'sc') {

                $sku = 'item-teste-sc';
            } elseif ($warehouse == 'sc_sp') {

                $sku = 'item-teste';
            }

            $shopCart = [
                [
                    'price' => floatval($price),
                    'qt' => $qt
                ]
            ];

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

            $cartValue = shopCartCalculator($shopCart); // === função que calcula valor do carrinho

            $qFusion = new Fusion;
            $quotation = $qFusion->fusion($cep, $channel, $products, $cartValue);

            $responseTreatment = new ResponserController;
            $res = $responseTreatment->returnQuotation($quotation, $deadline);

            if ($res['Type'] == 'Error') {

                $response = $responseTreatment->responseClient($response, $res['Message'], 400);

                return $response;
            }

            $response = $responseTreatment->responseClient($response, $res, 200);

            return $response;
        } catch (\Exception $e) {

            $messageResponse = $e->getMessage();

            $res = new ResponserController;
            $response = $res->responseClient($response, $messageResponse, 400);
            return $response;
        }
    }
}
