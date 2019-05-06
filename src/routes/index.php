<?php

use Slim\App;

$welcome = require __DIR__ . '/welcome/index.php';

$routes = [$welcome];

return function (App $app) use ($routes) {
    foreach ($routes as $route) {
        $route($app);
    }
};
