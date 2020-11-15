<?php

namespace App\Tests;

use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

class MemberTest extends TestCase
{
    private $client;
    private $token;

    function __construct($name = null, array $data = [], $dataName = '') {
        $this->client = new HttpClient([
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/auth/token', [
            'json' => [
                "username" => "api",
                "password" => $_ENV['API_PASSWORD']
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $this->token = $data['token'];

        parent::__construct($name, $data, $dataName);
    }

    public function testRegisterMemberWithoutAuth() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('JWT Token not found', $data['message']);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testRegisterMemberWithoutData() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->arrayHasKey($data['errors']);
        $this->assertEquals(5, count($data['errors']));
    }

    public function testRegisterMemberInvalidDateAndPhone() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token
            ],
            'body' => json_encode([
                'email' => 'adam.nowak@gmail.com',
                'firstname' => 'Adam',
                'lastname' => 'Nowak',
                'phone' => '123',
                'birthdate' => '111'
            ])
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertEquals("Phone number length is not valid. Your phone has 3 length, 9 is required.", $data['errors']['phone']);
        $this->assertEquals("Invalid date format. Accepted format is only YYYY-MM-DD.", $data['errors']['birthdate']);
    }

    public function testRegisterMemberUnder18AndPhoneStartByZero() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token
            ],
            'body' => json_encode([
                'email' => 'adam.nowak@gmail.com',
                'firstname' => 'Adam',
                'lastname' => 'Nowak',
                'phone' => '012345678',
                'birthdate' => '2006-01-01'
            ])
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertEquals("Your phone number starts from zero, it is invalid.", $data['errors']['phone']);
        $this->assertEquals("You have only 14 years old. 18 years is required.", $data['errors']['birthdate']);
    }

    public function testRegisterWithNonExistingCurrency() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token
            ],
            'body' => json_encode([
                'email' => 'adam.nowak@gmail.com',
                'firstname' => 'Adam',
                'lastname' => 'Nowak',
                'phone' => '123456789',
                'birthdate' => '1990-01-01',
                'currencies' => [
                    'xxx' => [
                        'min' => 2.1,
                        'max' => 3.5
                    ]
                ]
            ])
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertTrue(in_array('Currency XXX not found.', $data['errors']['currencies']));
    }

    public function testRegisterWithCurrencyMinLargerThanMax() {
        $response = $this->client->post('http://'.$_ENV['DOCKER_APACHE_HOST'].'/api/member/register', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token
            ],
            'body' => json_encode([
                'email' => 'adam.nowak@gmail.com',
                'firstname' => 'Adam',
                'lastname' => 'Nowak',
                'phone' => '123456789',
                'birthdate' => '1990-01-01',
                'currencies' => [
                    'usd' => [
                        'min' => 3.1,
                        'max' => 2.5
                    ]
                ]
            ])
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertTrue(in_array('Currency maximal notify value have to be higher than minimal for currency USD.', $data['errors']['currencies']));
    }
}
