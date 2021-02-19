<?php

namespace App\Controllers;

use App\Util\Core;
use App\Util\E10Bi;
use App\Util\Excel;
use App\Util\Fusion;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function Src\{deParaChannel, moveUploadedFile, shopCartCalculator};

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

        $directory = 'Uploads/';
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['formPedidos'];

        die(print_r($uploadedFile));


        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

            // === Move arquivo da pasta tmp para a pasta uploads
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
                $cep = $data->DeliveryPostalCode;

                $channelOriginOrder = $data->MarketPlaceBrand;
                $channel = deParaChannel($channelOriginOrder);

                if ($channel == 'Error') {

                    $res = new ResponserController;
                    $response = $res->responseClient($response, "Canal não encontrado", 200, "Error");;

                    return $response;
                }

                $shopCart = [];
                $products = [];

                foreach ($data->Items as $i) {

                    $sku = $i->ProductID . '-' . $i->SkuID;
                    $qt = $i->Qty;
                    $price = $i->Price;

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

                $qFusion = new Fusion;
                $quotation = $qFusion->quotationSimpleFusion($cep, $channel, $products, $cartValue);

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

            $excel->writePlanOrders($uploadfile, $resultQuotation);;

            $res = new ResponserController;
            $response = $res->responseClient($response, $uploadfile, 200, "Success");
        }
        return $response;
    }
}
