<?php

namespace App\Controllers;

use App\Models\Products;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductsController
{

    /**
     * Busca preço produto
     *
     * @param string $idItem
     * @return void
     */

    public function getItems(string $idItem)
    {
        $pd = Products::where('id_item', '=', $idItem)->first();

        return $pd;
    }


    /**
     * Busca todos os produtos
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getAllProducts(Request $request, Response $response): Response
    {
        $limit = $request->getQueryParams()['limit'] ?? null;
        $page = $request->getQueryParams()['page'] ?? null;

        try {
            if ($limit != null && $page != null) {

                $currentPage = ($page - 1) * $limit;
                $pd = Products::limit($limit)->offset($currentPage)->get();

                $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                $response->getBody()->write(json_encode($pd));
            } else {

                $pd = Products::all();

                $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                $response->getBody()->write(json_encode($pd));
            }

            return $response;
        } catch (\Exception $e) {

            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            $response->getBody()->write(json_encode($e->getMessage()));
            return $response;
        }
    }
}
