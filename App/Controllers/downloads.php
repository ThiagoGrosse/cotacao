<?php

namespace App\Controllers;

use App\Models\Products;

class Downloads
{


    /**
     * Função que atualiza preço dos produtos no banco de dados
     */
    public function downloadPrice()
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

        $var = 0;
        $result = [];

        foreach ($getDados as $i) {

            $var++;

            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $priceDe = str_replace(',', '.', $i['preco_de']);
            $pricePor = str_replace(',', '.', $i['preco_por']);

            $totalRegistros = count($getDados);
            echo 'Processando ' . $var . ' de ' . $totalRegistros;

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
                     * Cadastra produto no banco de dados
                     */

                    Products::create(['id_item' => $idItem, 'price' => $finalPrice]);

                    echo ' cadastrado' . PHP_EOL;

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto cadastrado'
                    ];
                } else {

                    echo ' atualizado' . PHP_EOL;

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto atualizado'
                    ];
                }
            } catch (\Exception $e) {

                $msg = $e->getMessage();

                $response = json_encode($msg);

                return $response;
            }
        }

        $msg = [
            'type' => 'Success',
            'timeThatWasCompleted' => date("H:i:s"),
            'data' => $result
        ];

        $response = json_encode($msg);

        return $response;
    }



    /**
     * Função que busca dados de prazo fornecedor dos produtos no E360
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function downloadDeadline()
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
        $var = 0;

        foreach ($getDados as $i) {

            $var++;

            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $deadline = intval($i['tempo_reposicao']);


            $totalRegistros = count($getDados);
            echo 'Processando ' . $var . ' de ' . $totalRegistros;


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

                    echo ' cadastrado' . PHP_EOL;

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto cadastrado'
                    ];
                } else {

                    echo ' atualizado' . PHP_EOL;

                    $result[] = [
                        'id_item' => $idItem,
                        'msg' => 'Produto atualizado'
                    ];
                }
            } catch (\Exception $e) {

                $response = json_encode($e->getMessage());

                return $response;
            }
        }

        $response = json_encode($result);

        return $response;
    }



    /**
     * Função que busca dados da planilha de skus do CORE no E360
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */

    public function downloadSku()
    {

        $getDados = [];

        /**
         * Busca e lê dados da planilha no sistema do E360
         */
        $handle = fopen('https://e360.estrela10.com.br/produtos/core/export', 'r');
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {

            if ($data[0] != 'ID Bseller' && $data[0] != '' && $data[10] == '6' && $data[6] == 1 && $data[7] == 1) {
                $getDados[] = [
                    'id_item' => $data[0],
                    'sku_core' => $data[5],
                ];
            }
        }


        $result = [];
        $var = 0;


        foreach ($getDados as $i) {

            $var++;

            /**
             * Coleta os dados
             */

            $idItem = $i['id_item'];
            $sku = $i['sku_core'];



            $totalRegistros = count($getDados);
            echo 'Processando ' . $var . ' de ' . $totalRegistros;



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


                        echo ' atualizado' . PHP_EOL;

                        $result[] = [
                            'id_item' => $idItem,
                            'msg' => 'Sku do produto foi atualizado'
                        ];
                    } else { // === Se retornar false


                        echo ' erro ao atualizar sku' . PHP_EOL;

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
        $response = json_encode($result);

        return $response;
    }
}
