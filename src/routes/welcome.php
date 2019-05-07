<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $this->logger->info('root');
        // $this->flash->addMessageNow('Test', 'Now-message'); - instant Message
        $flash = $this->flash->getMessages();

        return $this->renderer->render($response, 'welcome/root.phtml', [
            'flash' => $flash
        ]);
    })->setName('root');
};
