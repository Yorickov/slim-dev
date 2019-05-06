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
            'base_uri' => 'http://localhost:8000'
        ]);
    }
    
    public function testRoot()
    {
        $expected = 'Slim';
        $response = $this->client->get('/');
        // $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString($expected, $body);
    }
}
