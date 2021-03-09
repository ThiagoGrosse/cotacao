<?php

namespace Src;

use App\Controllers\ResponserController;

function validateInformation($response, $info)
{
    $res = new ResponserController;
    $response = $res->responseClient($response, 'É necessário informar ' . $info, 400, "Error");

    return $response;
}
