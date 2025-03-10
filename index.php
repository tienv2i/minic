<?php
require_once "./vendor/autoload.php";
require_once "./src/common.php";


logAccess(__DIR__."/logs/");
rateLimit(100, 1000, __DIR__."/logs");

use Minic\Core\Bootstrap;
$app = new Bootstrap();
$app->run_app();