<?php

use Slim\App;

$welcome = require __DIR__ . '/welcome.php';
$posts = require __DIR__ . '/posts.php';

$routes = [$welcome, $posts];

return function (App $app) use ($routes) {
    foreach ($routes as $route) {
        $route($app);
    }
};
