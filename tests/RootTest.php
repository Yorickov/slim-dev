<?php

namespace Slim\Hx\Tests;

use \PHPUnit\Framework\TestCase;
use \Symfony\Component\Process\Process;

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
        $expected = 'Slim';
        $response = $this->client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
        
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString($expected, $body);
    }

    public function testPosts()
    {
        $this->client->get('/');
        $response = $this->client->get('/posts');
        $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();

        $this->assertStringContainsString('Itaque quibusdam', $body);
        $this->assertStringNotContainsString('Est placeat rerum', $body);

        $response2 = $this->client->get('/posts?page=2');
        $this->assertEquals(200, $response->getStatusCode());
        
        $body2 = $response2->getBody()->getContents();
        $this->assertStringContainsString('?page=1', $body2);
        $this->assertStringContainsString('?page=3', $body2);

        $this->assertStringNotContainsString('Itaque quibusdam tempora', $body2);
        $this->assertStringContainsString('Est placeat rerum', $body2);
    }
}
