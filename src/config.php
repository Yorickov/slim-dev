<?php

return [
    'app' => [
        'settings' => [
            'displayErrorDetails' => true, // set to false in production
            'addContentLengthHeader' => false
        ]
    ],
    'renderer' => [
        'path' => __DIR__ . '/../templates/',
        'options' => [
            'cache' => __DIR__ . '/../var/cache'
        ]
    ],
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../var/logs/app.log',
        'level' => \Monolog\Logger::DEBUG
    ]
];
