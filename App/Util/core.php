<?php

namespace App\Util;

class Core
{
    protected $user;
    protected $pass;
    public $url;

    public function __construct()
    {
        $this->user = getenv('USER_NAME_CORE');
        $this->pass = getenv('PASSWORD_CORE');
        $this->url = 'http://layer.estrela10.corecommerce.com.br/v1/Sales/API.svc/web/GetOrderByNumber';
    }

    public function getOrderByCore($idPedido)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($idPedido));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $result = json_decode(curl_exec($ch));

        curl_close($ch);

        return $result;
    }
}
