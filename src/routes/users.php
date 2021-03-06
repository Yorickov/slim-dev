<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Dev\Models\User;

return function (App $app) {
    $app->get('/users/new', function (Request $request, Response $response) {
        $this->renderer->render($response, 'users/new.phtml', [
            'userData' => [],
            'errors' => []
        ]);
    })->setName('users#new');

    $app->post('/users', function (Request $request, Response $response) {
        $userData = $request->getParsedBodyParam('user');
        $errors = $this->userMapper->validate($userData);
    
        if (count($errors) === 0) {
            $user = new User($userData['nickname'], $userData['password']);

            $id = $this->userMapper->save($user);
            $this->flash->addMessage('success', 'User has been created');
            return $response->withHeader('X-USERID', $id)
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
    
    $app->post('/session', function ($request, $response) {
        $userData = $request->getParsedBodyParam('user');
        $users = $this->userMapper->all();

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
