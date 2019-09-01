<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class AuthClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(string $baseUrl, Client $httpClient)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
    }

    public function login($type, $username, $password): Response
    {
        return $this->httpClient->post($this->baseUrl.'/auth', [
            RequestOptions::JSON => [
                'type' => $type,
                'username' => $username,
                'password' => $password,
            ]
        ]);
    }
}
