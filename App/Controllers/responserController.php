<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class ResponserController
{


    /**
     * Retorno genérico para requisições
     *
     * @param Response $response
     * @param string $messageResponse
     * @param integer $idStatus
     * @param string $type
     * @return Response
     */

    public function responseClient(Response $response, string $messageResponse, int $idStatus, string $type): Response
    {
        $messageResponse = json_encode(["Type" => $type, "Message" => $messageResponse]);
        $response->getBody()->write($messageResponse);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($idStatus);
    }



    /**
     * Retorno de cotações
     *
     * @param Response $response
     * @param string $messageResponse
     * @param integer $idStatus
     * @return Response
     */

    public function responseQuotation(Response $response, string $messageResponse, int $idStatus): Response
    {
        $response->getBody()->write($messageResponse);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($idStatus);
    }



    /**
     * Aplica tratamento no retorno da cotação na Fusion
     *
     * @param Response $response
     * @param object $quotation
     * @param integer $deadline
     * @return Response
     */
    public function returnQuotation(Response $response, object $quotation, int $deadline): Response
    {

        if (is_null($quotation->modalidades)) {

            $result = [
                'Message' => $quotation->msg,
                'Protocolo' => $quotation->protocolo
            ];

            $res = new ResponserController;
            $response = $res->responseQuotation($response, json_encode($result), 400);

            return $response;
        }

        $valor = $quotation->modalidades[0]->valor;

        if ($valor == 0) {
            $valor = 'Grátis';
        }

        $result = [
            'protocolo' => $quotation->protocolo,
            'cdMicroServico' => $quotation->modalidades[0]->itens[0]->cdMicroServico,
            'nomeTransportadora' => $quotation->modalidades[0]->transportador,
            'prazo' => $quotation->modalidades[0]->prazo,
            'prazoTransit' => $quotation->modalidades[0]->prazoTransit,
            'prazoExpedicao' => $quotation->modalidades[0]->prazoExpedicao,
            'prazoProdutoBseller' => $deadline,
            'valor' => $valor,
            'custo' => $quotation->modalidades[0]->custo
        ];

        $res = new ResponserController;
        $response = $res->responseQuotation($response, json_encode($result), 200);
        return $response;
    }
}
