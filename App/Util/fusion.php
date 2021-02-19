<?php

namespace App\Util;

class Fusion
{

    protected $token;
    protected $postToken;
    public $url;


    /**
     * Dados para cotação no Fusion
     */
    public function __construct()
    {
        $this->token = getenv('TOKEN_FUSION_COTACAO');
        $this->postmanToken = getenv('TOKEN_FUSION_POSTMAN');
        $this->url = 'https://menufrete.uxsolutions.com.br/api/menuFrete/obter';
    }


    /**
     * Função que envia os dados ao Fusion
     */
    public function quotationSimpleFusion($cep, $channel, $products, $cartValue)
    {
        $data = [
            "cdPedido" => "9999",
            "cepDestino" => $cep,
            "tipoServico" => "Entrega",
            "canalVenda" => $channel,
            "cdCliente" => null,
            "itens" => $products,
            "vlrCarrinho" => $cartValue
        ];

        $json = json_encode($data);

        $ch = curl_init($this->url);
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
}
