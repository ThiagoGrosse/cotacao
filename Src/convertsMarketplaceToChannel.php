<?php

namespace Src;

function deParaChannel($channelOriginOrder)
{
    if (is_null($channelOriginOrder)) {

        $channel = 'Estrela 10';
    } elseif ($channelOriginOrder == 'CasasBahia' or $channelOriginOrder == 'Extra' or $channelOriginOrder == 'PontoFrio') {

        $channel = 'Via Varejo';
    } elseif ($channelOriginOrder == 'MagazineLuiza') {

        $channel = 'Magalu';
    } elseif ($channelOriginOrder == 'Shoptime' or $channelOriginOrder == 'Submarino' or $channelOriginOrder == 'Americanas') {

        $channel = 'B2W';
    } elseif ($channelOriginOrder == 'Carrefour') {

        $channel = 'Carrefour';
    } elseif ($channelOriginOrder == 'MercadoLivre' or $channelOriginOrder == 'MercadoLivre') {

        $channel = "Mercado L";
    } elseif ($channelOriginOrder == 'Store') {

        $channel = "Estrela 10";
    } else {
        $channel = 'Error';
    }

    return $channel;
}
