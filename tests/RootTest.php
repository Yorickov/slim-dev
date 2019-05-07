<?php

namespace Slim\Hx\Tests;

use \PHPUnit\Framework\TestCase;
use \Symfony\Component\Process\Process;

use GuzzleHttp\Exception\ClientException; // for 400-level errors
use GuzzleHttp\Exception\ServerException; // for 500-level errors
use GuzzleHttp\Exception\BadResponseException; // for both (it's their superclass)

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
        $this->client->get('/');
        $this->client->get('/posts');
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
}
