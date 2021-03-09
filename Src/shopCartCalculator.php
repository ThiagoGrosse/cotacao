<?php

namespace Src;

/**
 * Calcula valor de carrinho
 *
 * @param array $shopCart
 * @return float
 */

function shopCartCalculator(array $shopCart): float
{

    $valueCart = [];

    foreach ($shopCart as $i) {

        $price = $i['price'];
        $qt = $i['qt'];

        $valueCart[] = $price * $qt;
    }

    return round(array_sum($valueCart), 2);
}
