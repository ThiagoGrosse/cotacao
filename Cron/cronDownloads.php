<?php

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

// $inicio = date('H:i:s') . ' Inicio download preços';
// echo $inicio . PHP_EOL;

// $downloads->downloadPrice();

// $inicio = date('H:i:s') . ' Inicio download prazos';
// echo $inicio . PHP_EOL;

// $downloads->downloadDeadline();

// $inicio = date('H:i:s') . ' Inicio download skus';
// echo $inicio . PHP_EOL;

$skus = $downloads->downloadSku();

$fim = date('H:i:s') . " Fim";

echo $fim;
