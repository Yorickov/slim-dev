<?php

use Slim\App;

$welcome = require __DIR__ . '/welcome.php';
$posts = require __DIR__ . '/posts.php';
$users = require __DIR__ . '/users.php';

$routes = [$welcome, $posts, $users];

return function (App $app) use ($routes) {
    foreach ($routes as $route) {
        $route($app);
    }
};
