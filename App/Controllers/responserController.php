<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class ResponserController
{


    /**
     * Retorno genérico para requisições
     *
     * @param Response $response
     * @param integer $idStatus
     * @param string $type
     * @return Response
     */

    public function responseClient(Response $response, $messageResponse, int $idStatus): Response
    {
        $messageResponse = json_encode($messageResponse);
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
    public function returnQuotation($quotation, int $deadline)
    {

        if (is_null($quotation->modalidades)) {

            $result = [
                'Message' => $quotation->msg,
                'Protocolo' => $quotation->protocolo
            ];

            return $result;
        }

        $valor = number_format($quotation->modalidades[0]->valor, 2);
        $custo = number_format($quotation->modalidades[0]->custo, 2);

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
            'valor' => str_replace('.', ',', $valor),
            'custo' => str_replace('.', ',', $custo)
        ];

        return $result;
    }
}
