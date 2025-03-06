<?php

return [
    'GET' => [
        '/' => function ($app, $params) {
            header('Location: '.base_url('/quiz'));
            die();
        },
        '/quiz' => 'Minic\App\Controllers\QuizzController@index',
        '/quiz/{quiz_name:string}' => 'Minic\App\Controllers\QuizzController@quiz',
        
    ],
    'POST' => [

    ],
    'PUT' => [

    ],
    'DELETE' => [

    ],
];
