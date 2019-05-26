<?php

return [
    'app' => [
        'settings' => [
            'displayErrorDetails' => true, // in prod: false
            'addContentLengthHeader' => false
        ]
    ],
    'renderer' => [
        'path' => __DIR__ . '/../templates/',
        'options' => [
            false // in prod: 'cache' => __DIR__ . '/../var/cache'
        ]
    ],
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../var/logs/app.log',
        'level' => \Monolog\Logger::DEBUG
    ],
    'db' => [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_PERSISTENT => true
    ]
];
