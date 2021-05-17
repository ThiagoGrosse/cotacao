<?php

/**
 * Chama método da class Downloads
 * que faz a atualização dos prazos
 * de produtos no banco de dados
 */


use App\Controllers\Downloads;

require_once '../vendor/autoload.php';
require_once '../Config/database.php';
require_once '../Config/env.php';
require_once '../Src/startDB.php';

ini_set('max_execution_time', 0);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');
ini_set('memory_limit', '1024M');

$downloads = new Downloads;
$downloads->downloadDeadline();

$downloads = new Downloads;
$downloads->downloadPrice();

$downloads = new Downloads;
$skus = $downloads->downloadSku();
