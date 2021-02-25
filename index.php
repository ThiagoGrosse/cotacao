<?php

date_default_timezone_set('America/Sao_paulo');
set_time_limit(0);

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
require_once 'Routes/index.php';
