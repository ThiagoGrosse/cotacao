<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ShippingCompany;
use Exception;

class ShippingCompanyController
{


    /**
     * Função que busca as transportadoras no banco de dados
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function getShippingCompany(Request $request, Response $response, array $args): Response
    {
        // === Verifica se um id foi informado, caso não, adota valor null
        $id = $request->getQueryParams()['id'] ?? null;

        if (is_null($id)) {

            try {

                // === Consulta todas as transportadoras
                $data = ShippingCompany::orderBy('id', 'ASC')->get();
                $data = json_encode($data);
                $response->getBody()->write($data);

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (Exception $e) {

                $response->getBody()->write($e->getMessage());

                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } else {

            try {

                // === Consulta transportadora pelo ID
                $data = ShippingCompany::Where('id', '=', $id)->get();
                $response->getBody()->write($data);

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (Exception $e) {

                $response->getBody()->write($e->getMessage());

                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }
    }



    /**
     * Função que atualiza o nome da transportadora na tabela de abrangência
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function updateShippingCompany(Request $request, Response $response, array $args): Response
    {
        // === Pega dados enviados pelo usuário
        $data = $request->getParsedBody();
        $newNameShippingCompany = $data['nmTransportadora'] ?? null;
        $id = $data['idTransportadora'] ?? null;


        if (is_null($id)) {

            $res = new ResponserController;
            $response = $res->responseClient($response, 'É necessário informar um ID', 400, 'Error');

            return $response;
        }

        if (is_null($newNameShippingCompany)) {

            $res = new ResponserController;
            $response = $res->responseClient($response, 'É necessário informar uma transportadora', 400, 'Error');

            return $response;
        }

        try {

            $updateTransp = ShippingCompany::find($id);
            $updateTransp->shipping_company_name = $newNameShippingCompany;
            $updateTransp->save();

            $messageResponse = 'Dados atualizados com sucesso!';

            $res = new ResponserController;
            $response = $res->responseClient($response, $messageResponse, 200, "Success");
            return $response;
        } catch (\Exception $e) {

            $res = new ResponserController;
            $response = $res->responseClient($response, $e->getMessage(), 400, "Error");
            return $response;
        }
    }
}
