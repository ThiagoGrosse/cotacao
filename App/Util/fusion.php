<?php

namespace App\Util;

class Fusion
{

    protected $token;
    protected $postToken;


    /**
     * Dados para cotação no Fusion
     */

    public function __construct()
    {
        $this->token = getenv('TOKEN_FUSION_COTACAO');
        $this->postmanToken = getenv('TOKEN_FUSION_POSTMAN');
    }



    /**
     * Cotação de frete  ->  Site E10
     */

    public function fusion($cep, $channel, $products, $cartValue)
    {
        $data = [
            "cdPedido" => "9999",
            "cepDestino" => $cep,
            "tipoServico" => "Entrega",
            "canalVenda" => $channel,
            "itens" => $products,
            "vlrCarrinho" => $cartValue
        ];

        $json = json_encode($data);

        $ch = curl_init('https://menufrete.uxsolutions.com.br/api/menuFrete/obter');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Token: ' . $this->token,
            'Postman-Token: ' . $this->postmanToken
        ));

        $result = json_decode(curl_exec($ch));

        curl_close($ch);

        return $result;
    }



    // /**
    //  * Cotação de frete  ->  B2W
    //  */

    // public function b2w($cep, $products)
    // {

    //     $data = [
    //         "destinationZip" => intval($cep),
    //         "volumes" => $products
    //     ];

    //     $json = json_encode($data);

    //     $ch = curl_init('https://appfusionmf-b2w.azurewebsites.net/api/menuFrete/skyhub/63b4ad9a-595e-4017-8bf3-2bceaa554705/');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }



    // /**
    //  * Cotação de frete  ->  Magalu
    //  */

    // public function magalu($cep, $products)
    // {

    //     $data = [
    //         "session_id" => "23sw",
    //         "zipcode" => $cep,
    //         "Items" => $products
    //     ];

    //     $json = json_encode($data);

    //     $ch = curl_init('https://menufrete-core.azurewebsites.net/api/menuFrete/magalu/63b4ad9a-595e-4017-8bf3-2bceaa554705/');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }



    // /**
    //  * Cotação de frete  ->  Mercado Livre
    //  */

    // public function mercadoLivre($cep, $products)
    // {

    //     $data = [
    //         "buyer_id" => 1,
    //         "destination" => $cep,
    //         "items" => $products
    //     ];

    //     $json = json_encode($data);

    //     $ch = curl_init('https://menufrete-core-prod1.azurewebsites.net/api/menuFrete/mercadoLivre/63b4ad9a-595e-4017-8bf3-2bceaa554705/');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }



    // /**
    //  * Cotação de frete  ->  Carrefour
    //  */

    // public function carrefour($cep, $sku, $qt)
    // {

    //     $ch = curl_init('https://menufrete.uxsolutions.com.br/api/menuFrete/carrefour/63b4ad9a-595e-4017-8bf3-2bceaa554705/freight/?sku=' . $sku . '|' . $qt . '&shipping_zip_code=' . $cep . '&token=63b4ad9a-595e-4017-8bf3-2bceaa554705');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }



    // /**
    //  * Cotação de frete  ->  Via Varejo
    //  */

    // public function viaVarejo($cep, $sku)
    // {

    //     $ch = curl_init('https://menufrete.uxsolutions.com.br/api/menuFrete/viavarejo/63b4ad9a-595e-4017-8bf3-2bceaa554705/freight/?skuId=' . $sku . '&zipCode=' . $cep . '&token=63b4ad9a-595e-4017-8bf3-2bceaa554705');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }



    // /**
    //  * Cotação de frete  ->  Homolog
    //  */

    // public function homolog($cep, $products, $cartValue)
    // {
    //     $data = [
    //         "cdPedido" => "9999",
    //         "cepDestino" => $cep,
    //         "tipoServico" => "Homolog",
    //         "canalVenda" => "Estrela 10",
    //         "itens" => $products,
    //         "vlrCarrinho" => $cartValue
    //     ];

    //     $json = json_encode($data);

    //     $ch = curl_init('https://appfusionmf-publish2.azurewebsites.net/api/menuFrete/obter');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Token: ' . $this->token,
    //         'Postman-Token: ' . $this->postmanToken
    //     ));

    //     $result = json_decode(curl_exec($ch));

    //     curl_close($ch);

    //     return $result;
    // }
}
