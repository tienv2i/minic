<?php
use Minic\Core\Bootstrap;
use Minic\Core\View;

function base_url ($path = "") {
    $app = Bootstrap::getInstance();
    if ($app->getConfig("app.static_url", "") !== "" ) { 
        $base_url = $app->getConfig("app.base_url", "");
    } else {
        $base_url = $app->getRequest()->get_base_url();
    } 
    return rtrim($base_url, "/").$path;
}
function static_url ($path = "") {
    $app = Bootstrap::getInstance();

    if ($app->getConfig("app.static_url", "") !== "" ) {
        return $app->getConfig("app.static_url").$path;
    }
    else {
        return base_url("/static".$path);
    }
}

View::addFunction("base_url", "base_url");
View::addFunction("static_url", "static_url");