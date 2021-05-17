<?php

ini_set('max_execution_time', 0);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');
ini_set('memory_limit', '1024M');

require_once 'vendor/autoload.php';
require_once 'Config/env.php';
require_once 'Config/database.php';
require_once 'Src/slimConfiguration.php';
require_once 'Src/startDB.php';
require_once 'Src/moveFiles.php';
require_once 'Src/shopCartCalculator.php';
require_once 'Src/convertsMarketplaceToChannel.php';
require_once 'Src/validateInfo.php';
require_once 'Src/channelList.php';
require_once 'Src/removeArquivos.php';
require_once 'Routes/index.php';
