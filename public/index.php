<?php

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

$config = require __DIR__ . '/../src/config.php';
$app = new \Slim\App($config['app']);

$injectDeps = require __DIR__ . '/../src/container.php';
$injectDeps($app, $config);

$appRoutes = require __DIR__ . '/../src/routes/index.php';
$appRoutes($app);

$app->run();
