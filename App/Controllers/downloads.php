<?php

namespace App\Controllers;

use App\Models\Products;

class Downloads
{


    /**
     * Método que busca dados de planilha no sistema E360
     * e atualiza preço dos produtos no banco de dados
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function downloadPrice()
    {

        $getDados = [];

        /**
         * Busca e lê dados da planilha no sistema do E360
         */

        $handle = fopen('http://187.73.187.22:3333/files/preco_venda.csv', 'r');
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
        $totalRegistros = count($getDados);
        $x = 0;

        foreach ($getDados as $i) {

            $x++;
            echo $x . '        de        ' . $totalRegistros . PHP_EOL;

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
                     * Cadastra produto no banco de dados
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

                $msg = $e->getMessage();

                return $msg;
            }
        }

        $msg = [
            'type' => 'Success',
            'timeThatWasCompleted' => date("H:i:s"),
            'data' => $result
        ];

        return $msg;
    }



    /**
     * Método que busca dados de planilha no sistema E360
     * e atualiza prazo de fornecedor dos produtos no banco de dados
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
        $handle = fopen('http://187.73.187.22:3333/files/item-fornecedor.csv', 'r');
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
        $totalRegistros = count($getDados);
        $x = 0;


        foreach ($getDados as $i) {


            $x++;
            echo $x . '        de        ' . $totalRegistros . PHP_EOL;


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


                return $e->getMessage();
            }
        }

        return $result;
    }



    /**
     * Método que busca dados de planilha no sistema E360
     * e atualiza sku CORE dos produtos no banco de dados
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

            if ($data[0] != 'ID Bseller' && $data[0] != '' && $data[6] == 1 && $data[7] == 1 && $data[10] == 6) {
                $getDados[] = [
                    'id_item' => $data[0],
                    'sku_core' => $data[5]
                ];
            }
        }

        $result = [];
        $totalRegistros = count($getDados);
        $x = 0;


        foreach ($getDados as $i) {


            $x++;
            echo $x . '        de        ' . $totalRegistros . PHP_EOL;


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

        return $result;
    }
}
