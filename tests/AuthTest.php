<?php

namespace App\Tests;

use \GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    private $client;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->client = new HttpClient([
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        parent::__construct($name, $data, $dataName);
    }

    public function testGetJWTTokenWithoutCredentials()
    {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/auth/token', [
            'http_errors' => false
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('errors', $data);
    }

    public function testGetJWTToken()
    {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/auth/token', [
            'json' => [
                "username" => "api",
                "password" => $_ENV['API_PASSWORD']
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $data);
    }
}
