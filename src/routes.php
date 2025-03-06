<?php

return [
    'GET' => [
        // '/' => function ($app, $params) {
        //     $app->render('home/index.html');
        // },
        '/quizz' => 'Minic\App\Controllers\QuizzController@index',
        '/quizz/{quizz_name:string}' => 'Minic\App\Controllers\QuizzController@quizz',
        
    ],
    'POST' => [

    ],
    'PUT' => [

    ],
    'DELETE' => [

    ],
];
