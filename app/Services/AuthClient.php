<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

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

    public function login($type, $username, $password): ResponseInterface
    {
        return $this->httpClient->post($this->baseUrl.'/user/login', [
            RequestOptions::JSON => [
                'type' => $type,
                'username' => $username,
                'password' => $password,
                'repository_type' => config('repository_type'),
            ]
        ]);
    }
}
