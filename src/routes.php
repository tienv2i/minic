<?php
use Minic\Core\Bootstrap;

return [
    'GET' => [
        '/' => function ($app, $params) {
            header('Location: '.base_url('/quiz'));
            die();
        },
        '/quiz' => 'Minic\App\Controllers\QuizzController@index',
        '/quiz/{quiz_name:string}' => 'Minic\App\Controllers\QuizzController@quiz',
        '/auth_clean' => 'Minic\Core\Bootstrap@auth_clean_view',

        
    ],
    'POST' => [
        '/auth' => 'Minic\Core\Bootstrap@auth_view',
    ],
    'PUT' => [

    ],
    'DELETE' => [

    ],
];
