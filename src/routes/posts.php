<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$posts = Slim\Dev\Generator::generate(100);

return function (App $app) use ($posts) {
    $app->get('/posts', function (Request $request, Response $response) use ($posts) {
        $page = $request->getQueryParam('page', 1);
        $per = $request->getQueryParam('per', 5);
        
        $offset = ($page - 1) * $per;
        $part = array_slice($posts, $offset, $per);

        $this->renderer->render($response, 'posts/index.phtml', [
            'posts' => $part,
            'prev' => $page < 2 ? 1 : $page - 1,
            'next' => $page + 1,
        ]);
    })->setName('posts#index');

    $app->get('/posts/{slug}', function (Request $request, Response $response, $args) use ($posts) {
        $post = collect($posts)->firstWhere('slug', $args['slug']);
        if (!$post) {
            return $response->withStatus(404)->write('Page not found'); // template!!!
        }
        return $this->renderer->render($response, 'posts/show.phtml', [
            'post' => $post
        ]);
    })->setName('post#show');
};
