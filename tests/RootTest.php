<?php

namespace Slim\Hx\Tests;

use \PHPUnit\Framework\TestCase;
use \Symfony\Component\Process\Process;

use GuzzleHttp\Exception\ClientException; // for 400-level errors
// use GuzzleHttp\Exception\ServerException; - for 500-level errors
// use GuzzleHttp\Exception\BadResponseException; - for both (it's their superclass)

class RootTest extends TestCase
{
    private $client;
    private static $process;

    public static function setUpBeforeClass(): void
    {
        self::$process = new Process('php -S localhost:8000 -t public public/index.php');
        self::$process->start();

        usleep(100000);
    }

    public function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'cookies' => true
        ]);
    }
    
    public function testRoot()
    {
        $response = $this->client->get('/');
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->client->get('/posts?page=2');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('?page=1', $body);
        $this->assertStringContainsString('?page=3', $body);
    }

    public function testPosts()
    {
        $response = $this->client->get('/posts/new');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('post[name]', $body);
        $this->assertStringContainsString('post[body]', $body);

        $formParams = ['post' => ['name' => '', 'body' => '']];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams,
            'http_errors' => false
        ]);
        $this->assertEquals(422, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString("Can not be blank", $body);

        $formParams = ['post' => ['name' => 'first', 'body' => 'last']];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams
        ]);
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('Post has been created', $body);
        $this->assertStringContainsString("first", $body);

        $formParams = ['post' => ['name' => 'second', 'body' => 'another']];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams
        ]);
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('Post has been created', $body);
        $this->assertStringContainsString('first', $body);
        $this->assertStringContainsString('second', $body);
    }

    public function testPost()
    {
        $formParams = ['post' => ['id' => 101, 'name' => 'first', 'body' => 'last']];
        $response = $this->client->post('/posts', [
            'form_params' => $formParams
        ]);
        $response = $this->client->get('/posts?page=2');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('?page=1', $body);
        $this->assertStringContainsString('?page=3', $body);
        
        $response2 = $this->client->get('/posts/101');
        $body2 = $response2->getBody()->getContents();
        $this->assertStringContainsString('last', $body2);

        $idFalse = 102;
        try {
            $this->client->get("/posts/{$idFalse}");
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(404, $response->getStatusCode());
        }
    }

    public function testUpdatePost()
    {
        $nameValue = 'first';
        $bodyValue = 'last';
        $formParams = ['post' => ['name' => $nameValue, 'body' => $bodyValue]];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams,
            'allow_redirects' => false
        ]);
        $id = $response->getHeaderLine('X-ID');
        $this->assertEquals(302, $response->getStatusCode());
        $response = $this->client->get('/posts');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString($nameValue, $body);

        $url = "/posts/{$id}/edit";
        $response = $this->client->get($url);
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString($nameValue, $body);

        $newNameValue = 'new-name';
        $formParams = ['post' => ['name' => $newNameValue, 'body' => $bodyValue]];
        $response = $this->client->patch("/posts/{$id}", [
            'form_params' => $formParams,
            'allow_redirects' => false
        ]);
        $this->assertEquals(302, $response->getStatusCode());
        $response = $this->client->get('/posts');
        $body = $response->getBody()->getContents();
        $this->assertStringNotContainsString($nameValue, $body);
        $this->assertStringContainsString($newNameValue, $body);
    }

    public function testUpdateWithErrors()
    {
        $nameValue = 'first';
        $bodyValue = 'last';
        $formParams = ['post' => ['name' => $nameValue, 'body' => $bodyValue]];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams,
            'allow_redirects' => false
        ]);
        $id = $response->getHeaderLine('X-ID');
        $this->assertEquals(302, $response->getStatusCode());

        $formParams = ['post' => ['name' => '', 'body' => '']];
        $response = $this->client->patch("/posts/{$id}", [
            'form_params' => $formParams,
            'allow_redirects' => false,
            'http_errors' => false
        ]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testDeletePost()
    {
        $name = 'first';
        $formParams = ['post' => ['name' => $name, 'body' => 'last']];
        $response = $this->client->post('/posts', [
            /* 'debug' => true, */
            'form_params' => $formParams,
            'allow_redirects' => false
        ]);
        $id = $response->getHeaderLine('X-ID');
        $this->assertEquals(302, $response->getStatusCode());

        $response = $this->client->delete("/posts/{$id}", [
            /* 'debug' => true, */
            'allow_redirects' => false
        ]);
        $this->assertEquals(302, $response->getStatusCode());
        $response = $this->client->get('/posts');
        $body = $response->getBody()->getContents();
        $this->assertStringNotContainsString($name, $body);
    }

    public function testCreateUserLogin()
    {
        $response = $this->client->get('/');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('Sign In', $body);

        $formParams = [
            'user' => [
                'nickname' => 'admin',
                'password' => 'secret'
            ]
        ];

        $response1 = $this->client->get('/users/new');
        $body1 = $response1->getBody()->getContents();
        $this->assertStringContainsString('User', $body1);

        $response2 = $this->client->get('/session/new');
        $this->assertEquals(200, $response2->getStatusCode());

        $this->client->post('/users', [
            'form_params' => $formParams
        ]);

        $response3 = $this->client->post('/session', [
            /* 'debug' => true, */
            'form_params' => $formParams
        ]);
        $body3 = $response3->getBody()->getContents();
        $this->assertStringContainsString('Sign Out', $body3);

        $response4 = $this->client->delete('/session', []);
        $body4 = $response4->getBody()->getContents();
        $this->assertStringContainsString('Sign In', $body4);
    }

    public function testLoginFail()
    {
        $formParams = [
            'user' => [
                'nickname' => 'nick',
                'password' => 'secret'
            ]
        ];
        $formParamsFalseName = [
            'user' => [
                'nickname' => 'wrong',
                'password' => 'secret'
            ]
        ];
        $formParamsFalsePass = [
            'user' => [
                'nickname' => 'nick',
                'password' => 'wrong'
            ]
        ];
        $this->client->post('/users', [
            'form_params' => $formParams
        ]);

        $response1 = $this->client->post('/session', [
            /* 'debug' => true, */
            'form_params' => $formParamsFalseName
        ]);
        $body1 = $response1->getBody()->getContents();
        $this->assertStringContainsString('Wrong', $body1);

        $response2 = $this->client->post('/session', [
            /* 'debug' => true, */
            'form_params' => $formParamsFalsePass
        ]);
        $body2 = $response2->getBody()->getContents();
        $this->assertStringContainsString('Wrong', $body2);
    }

}
