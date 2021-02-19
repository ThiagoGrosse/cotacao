<?php

namespace App\Controllers;

use App\Models\Products;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Downloads
{

    /**
     * Função que atualiza preço dos produtos no banco de dados
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function downloadPrice(Request $request, Response $response): Response
    {

        $getDados = [];

        /**
         * Busca e lê dados da planilha no sistema do E360
         */

        $handle = fopen('http://187.73.184.135:3333/files/preco_venda.csv', 'r');
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
            if ($data[0] != 'id_item') {
                $getDados[] = [
                    'id_item' => $data[0],
                    'preco_de' => $data[1],
                    'preco_por' => $data[2],
                ];
            }
        }

        $result = [];

        foreach ($getDados as $i) {


            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $priceDe = str_replace(',', '.', $i['preco_de']);
            $pricePor = str_replace(',', '.', $i['preco_por']);


            /**
             * Converte os valores
             */

            if (!isset($pricePor)) {

                $finalPrice = floatval($pricePor);
            } else {

                $finalPrice = floatval($priceDe);
            }


            try {


                /**
                 * Atualiza o preço dos produtos no banco
                 */

                $updateProduct = Products::where('id_item', '=', $idItem)->update(['price' => $finalPrice]);

                if ($updateProduct == 0) { // === Se o produto não for atualizado


                    /**
                     * Cadastro produto no banco de dados
                     */

                    Products::create(['id_item' => $idItem, 'price' => $finalPrice]);

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto cadastrado'
                    ];
                } else {

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto atualizado'
                    ];
                }
            } catch (\Exception $e) {

                $res = new ResponserController;
                $response = $res->responseClient($response, "Falhou, tente novamente", 400, "Error");

                return $response;
            }
        }

        $res = new ResponserController;
        $response = $res->responseClient($response, "Processo concluído", 200, "Success");

        return $response;
    }



    /**
     * Função que busca dados de prazo fornecedor dos produtos no E360
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function downloadDeadline(Request $request, Response $response): Response
    {

        $getDados = [];

        /**
         * Busca e lê dados da planilha no sistema do E360
         */
        $handle = fopen('http://187.73.184.135:3333/files/item-fornecedor.csv', 'r');
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {

            if ($data[0] != 'id_item' && $data[6] != 'N') {
                $getDados[] = [
                    'id_item' => $data[0],
                    'tempo_reposicao' => $data[5],
                    'in_ped_automatico' => $data[6]
                ];
            }
        }

        $result = [];

        foreach ($getDados as $i) {


            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $deadline = intval($i['tempo_reposicao']);
            $items[] = [
                'id' => $idItem,
                'prazo' => $deadline
            ];

            try {

                /**
                 * Atualiza prazo do produto no banco de dados
                 */

                $updateProduct = Products::where('id_item', '=', $idItem)->update(['deadline' => $deadline]);

                if ($updateProduct == 0) { // === Se o produto não foi atualizado


                    /**
                     * Cadastra produto no banco de dados
                     */

                    Products::create(['id_item' => $idItem, 'deadline' => $deadline]);

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto cadastrado'
                    ];
                } else {

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto atualizado'
                    ];
                }
            } catch (\Exception $e) {

                $res = new ResponserController;
                $response = $res->responseClient($response, $e->getMessage(), 400, "Error");

                return $response;
            }
        }

        $res = new ResponserController;
        $response = $res->responseClient($response, json_encode($result), 200, "Success");

        return $response;
    }



    /**
     * Função que busca dados da planilha de skus do CORE no E360
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function downloadSku(Request $request, Response $response): Response
    {

        $getDados = [];

        /**
         * Busca e lê dados da planilha no sistema do E360
         */
        $handle = fopen('https://e360.estrela10.com.br/produtos/core/export', 'r');
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {

            if ($data[0] != 'ID Bseller' && $data[0] != '' && $data[10] == '6') {
                $getDados[] = [
                    'id_item' => $data[0],
                    'sku_core' => $data[5]
                ];
            }
        }

        $result = [];

        foreach ($getDados as $i) {


            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $sku = $i['sku_core'];


            /**
             * busca produto no banco de dados
             */

            $getProductDb = Products::where('id_item', '=', $idItem)->first();

            if (empty($getProductDb)) { // === Se não encontrar o produto

                $result[] = [
                    'id_item' => $idItem,
                    'msg' => 'Produto não cadastrado'
                ];
            } else { // === Se encontrar o produto

                if ($getProductDb['sku_core'] != $sku) { // === Se o sku da planilha for diferente do sku que tem no banco

                    $updateSku = Products::where('id_item', '=', $idItem)->update(['sku_core' => $sku]); // === Atualiza sku no banco de dados

                    if ($updateSku != 0) { // === Se retornar diferente de falso ( true )

                        $result[] = [
                            'id_item' => $idItem,
                            'msg' => 'Sku do produto foi atualizado'
                        ];
                    } else { // === Se retornar false

                        $result[] = [
                            'id_item' => $idItem,
                            'msg' => 'Erro ao atualizar sku'
                        ];
                    }
                }
            }
        }


        /**
         * Retorna resultado geral
         */
        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        $response->getBody()->write(json_encode($result));

        return $response;
    }
}
