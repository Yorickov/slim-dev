<?php

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

$config = require __DIR__ . '/../src/config.php';

try {
    $ddl = new \Slim\Dev\Services\DDLManager('sqlite::memory:', null, null, $config['db']);
    $ddl->createTable('posts', [
        'id' => 'integer',
        'name' => 'string',
        'body' => 'string'
    ]);
    $ddl->createTable('users', [
        'id' => 'integer',
        'passwordDigest' => 'string',
        'nickname' => 'string'
    ]);
    $dbh = $ddl->getConnection();
} catch (DOException $e) {
    echo "Error!: {$e->getMessage()}";
    die();
}

session_start();

$app = new \Slim\App($config['app']);

$injectDeps = require __DIR__ . '/../src/container.php';
$injectDeps($app, $config, $dbh);

// $middleware = require __DIR__ . '/../src/middleware.php';
// $middleware($app);

$appRoutes = require __DIR__ . '/../src/routes/index.php';
$appRoutes($app);

$app->run();
