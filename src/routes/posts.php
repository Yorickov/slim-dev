<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

// $posts = Slim\Dev\Generator::generate(100);
$repo = new \Slim\Dev\Repository(); // add into Container

return function (App $app) use ($repo) {
    $app->get('/posts', function (Request $request, Response $response) use ($repo) {
        // $this->flash->addMessageNow('Test', 'Now-message'); - instant Message
        $flash = $this->flash->getMessages();
        
        $posts = $repo->all();
        $page = $request->getQueryParam('page', 1);
        $per = $request->getQueryParam('per', 5);
        
        $offset = ($page - 1) * $per;
        $part = array_slice($posts, $offset, $per);

        $this->renderer->render($response, 'posts/index.phtml', [
            'posts' => $part,
            'prev' => $page < 2 ? 1 : $page - 1,
            'next' => $page + 1,
            'flash' => $flash
        ]);
    })->setName('posts#index');

    $app->get('/posts/new', function (Request $request, Response $response) use ($repo) {
        $this->renderer->render($response, 'posts/new.phtml', [
            'postData' => [],
            'errors' => []
        ]);
    })->setName('posts#new');

    $app->get('/posts/{id}', function (Request $request, Response $response, $args) use ($repo) {
        $posts = $repo->all(); // find
        $post = collect($posts)->firstWhere('id', $args['id']);
        if (!$post) {
            $response = $response->withStatus(404);
            return $this->renderer->render($response, 'errors/404.phtml');
        }
        return $this->renderer->render($response, 'posts/show.phtml', [
            'post' => $post
        ]);
    })->setName('post#show');

    $app->post('/posts', function (Request $request, Response $response) use ($repo) {
        $postData = $request->getParsedBodyParam('post');
    
        $validator = new \Slim\Dev\Validator();
        $errors = $validator->validate($postData);
    
        if (count($errors) === 0) {
            $repo->save($postData);
            $this->flash->addMessage('success', 'Post has been created');
            return $response->withRedirect($this->router->pathFor('posts#index'));
        }
    
        $response = $response->withStatus(422);
        return $this->renderer->render($response, 'posts/new.phtml', [
            'postData' => $postData,
            'errors' => $errors
        ]);
    });
};
