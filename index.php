<?php
require_once "./vendor/autoload.php";
require_once "./src/common.php";

use Minic\Core\Bootstrap;
$app = new Bootstrap();
$app->run_app();