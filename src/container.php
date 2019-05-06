<?php

use Slim\App;

return function(App $app, $config) {
    $container = $app->getContainer();

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
};
