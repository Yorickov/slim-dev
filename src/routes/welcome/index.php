<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $this->logger->info('root');
        return $this->renderer->render($response, 'root.phtml', []);
    });
};
