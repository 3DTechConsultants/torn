<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class TornApiService
{
    const API_URL = 'https://api.torn.com/';
    private string $apiKey;
    private HttpClientInterface $httpClient;
    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }
    public function getUser(?int $userId = null): array
    {
        $response = $this->httpClient->request('GET', self::API_URL . 'user/' . $userId, [
            'query' => [
                'key' => $this->apiKey,
                'selections' => 'basic,profile'
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            dd($response->getContent()); // This will throw an exception if the response is not 200
            throw new \Exception('Failed to fetch data from the API.');
        }

        return $response->toArray();
    }

    public function getAttacks(): array
    {
        $response = $this->httpClient->request('GET', self::API_URL . 'user/',  [
            'query' => [
                'key' => $this->apiKey,
                'selections' => 'attacks',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            dd($response->getContent()); // This will throw an exception if the response is not 200
            throw new \Exception('Failed to fetch data from the API.');
        }

        return $response->toArray();
    }
}
