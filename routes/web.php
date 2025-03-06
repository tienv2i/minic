<?php

return [
    'GET' => [
        '/' => function ($app, $params) {
            $app->render('home/index.html');
        },
        '/hello/{name:string}' => function ($app, $params) {
            return "hello ". $params["name"];
        },
        // '/hello/{name:string}/{name2:string}' => function ($app, $params) {
        //     return "hello ". $params["name2"];
        // },
        // '/users/{id}'=> 'App\Controllers\UserController@show',  // Dạng string
        // '/posts/{slug}' => ['App\Controllers\PostController', 'view'],  // Dạng array
    ],
    'POST' => [

    ],
    'PUT' => [

    ],
    'DELETE' => [

    ],
];
