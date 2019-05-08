<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Dev\UserValidator as Validator;

$repo = new \Slim\Dev\UserRepository();

return function (App $app) use ($repo) {
    $app->get('/users/new', function (Request $request, Response $response) {
        $this->renderer->render($response, 'users/new.phtml', [
            'userData' => [],
            'errors' => []
        ]);
    })->setName('users#new');

    $app->post('/users', function (Request $request, Response $response) use ($repo) {
        $userData = $request->getParsedBodyParam('user');
    
        $validator = new Validator();
        $errors = $validator->validate($userData);
    
        if (count($errors) === 0) {
            $userId = $repo->save($userData);
            $this->flash->addMessage('success', 'User has been created');
            return $response->withHeader('X-USERID', $userId)
                            ->withRedirect($this->router->pathFor('posts#index'));
        }
    
        return $this->renderer->render($response->withStatus(422), 'users/new.phtml', [
            'userData' => $userData,
            'errors' => $errors
        ]);
    })->setName('users#create');

    $app->get('/session/new', function (Request $request, Response $response) {
        $this->renderer->render($response, 'sessions/new.phtml', [
            'userData' => [],
            'errors' => []
        ]);
    })->setName('session#new');
    
    $app->post('/session', function ($request, $response) use ($repo) {
        $userData = $request->getParsedBodyParam('user');
        $users = $repo->all(); // find

        $user = collect($users)->first(function ($user) use ($userData) {
            return $user['nickname'] == $userData['nickname']
                && hash('sha256', $userData['password']) == $user['passwordDigest'];
        });
    
        if ($user) {
            $_SESSION['user'] = $user;
        } else {
            $this->flash->addMessage('error', 'Wrong password or name');
        }
            return $response->withRedirect('/');
    })->setName('session#create');
    
    $app->delete('/session', function (Request $request, Response $response) {
        session_unset();
        session_destroy();
        return $response->withRedirect('/');
    })->setName('session#destroy');
};
