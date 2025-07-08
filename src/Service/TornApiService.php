<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class TornApiService
{
    const API_URL = 'https://api.torn.com/';
    const API_V2_URL = 'https://api.torn.com/v2';
    private string $apiKey;
    private HttpClientInterface $httpClient;
    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function getUser(?int $userId = null, array $selections = []): array
    {
        sleep(2); // Sleep to avoid hitting API rate limits
        $defaultSelections = ['basic', 'profile'];
        $selections = array_merge($defaultSelections, $selections);
        $response = $this->httpClient->request('GET', self::API_URL . 'user/' . $userId, [
            'query' => [
                'key' => $this->apiKey,
                'selections' => implode(',', $selections),
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
        return $this->API('user/', ['attacks']);
    }

    public function getAmmo(): array
    {
        return $this->API('user/', ['ammo']);
    }

    public function getLog(): array
    {
        return $this->API('user/', ['log']);
    }

    public function listBounties(): array
    {
        $limit = 100;
        $offset = 0;
        $bounties = [];
        $comment = 'Just fucking around';
        do {
            sleep(2);
            $response = $this->httpClient->request('GET', self::API_V2_URL . '/torn/bounties', [
                'query' => [
                    'key' => $this->apiKey,
                    'limit' => $limit,
                    'offset' => $offset,
                ],
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to fetch data from the API.');
            }
            $result = $response->toArray();
            if (isset($result['bounties'])) {
                $bounties = array_merge($bounties, $result['bounties']);
            } else {
                break;
            }
            $offset += $limit;
            print $offset . "\n";
        } while ($result['_metadata']['links']['next'] != null);
        return $bounties;
    }

    private function API(string $endpoint, array $selections = []): array
    {
        $response = $this->httpClient->request('GET', self::API_URL . $endpoint, [
            'query' => [
                'key' => $this->apiKey,
                'selections' => implode(',', $selections),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch data from the API.');
        }

        return $response->toArray();
    }
}
