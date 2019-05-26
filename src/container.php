<?php

use Slim\App;

return function (App $app, $config, \PDO $pdo) {
    $container = $app->getContainer();
    // var_dump ($pdo);
    $container['renderer'] = function ($c) use ($config) {
        $settings = $config['renderer'];
        $renderer = new \Slim\Views\Twig($settings['path'], $settings['options']);
        $router = $c->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $renderer->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        return $renderer;
    };

    $container['logger'] = function ($c) use ($config) {
        $settings = $config['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
    
    $container['flash'] = function () {
        return new \Slim\Flash\Messages();
    };

    $container['postRepo'] = function () {
        return new \Slim\Dev\Models\PostRepository();
    };

    $container['userRepo'] = function () {
        return new \Slim\Dev\Models\UserRepository();
    };

    $container['postMapper'] = function () use ($pdo) {
        return new \Slim\Dev\Services\PostMapper($pdo);
    };

    $container['userMapper'] = function () use ($pdo) {
        return new \Slim\Dev\Services\UserMapper($pdo);
    };
};
