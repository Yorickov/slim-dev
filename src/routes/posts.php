<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Dev\Validator;

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
            'page' => $page,
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
    
        $validator = new Validator();
        $errors = $validator->validate($postData);
    
        if (count($errors) === 0) {
            $id = $repo->save($postData);
            $this->flash->addMessage('success', 'Post has been created');
            return $response->withHeader('X-ID', $id)
                            ->withRedirect($this->router->pathFor('posts#index'));
        }
    
        return $this->renderer->render($response->withStatus(422), 'posts/new.phtml', [
            'postData' => $postData,
            'errors' => $errors
        ]);
    })->setName('posts#create');

    $app->get('/posts/{id}/edit', function (Request $request, Response $response, $args) use ($repo) {
        $post = $repo->find($args['id']);
        return $this->renderer->render($response, 'posts/edit.phtml', [
            'postData' => $post,
            'post' => $post,
            'errors' => []
        ]);
    })->setName('posts#edit');

    $app->patch('/posts/{id}', function (Request $request, Response $response, $args) use ($repo) {
        $post = $repo->find($args['id']);
        $postData = $request->getParsedBodyParam('post');
    
        $validator = new Validator();
        $errors = $validator->validate($postData);
    
        if (count($errors) === 0) {
            $post['name'] = $postData['name'];
            $post['body'] = $postData['body'];
            $repo->save($post);
            $this->flash->addMessage('success', 'Post has been updated');
            return $response->withRedirect($this->router->pathFor('posts#index'));
        }

        return $this->renderer->render($response->withStatus(422), 'posts/edit.phtml', [
            'post' => $post,
            'postData' => $postData,
            'errors' => $errors
        ]);
    })->setName('posts#update');

    $app->delete('/posts/{id}', function (Request $request, Response $response, $args) use ($repo) {
        $repo->destroy($args['id']);
        $this->flash->addMessage('success', 'Post has been removed');
        return $response->withRedirect($this->router->pathFor('posts#index'));
    })->setName('posts#destroy');
};
