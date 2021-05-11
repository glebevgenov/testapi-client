<?php

namespace Application;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Service\HttpClient;
use Service\HttpException;
use Service\HttpNotFoundException;

class GuzzleHttpClient implements HttpClient
{
    private Client $client;

    /**
     * @throws HttpException
     */
    public function request(string $method, string $absoluteUrl, array $params = []): void
    {
        try {
            $this->guzzleRequest($method, $absoluteUrl, $params)->getBody();
        } catch (GuzzleException $e) {
            throw $this->createException($e, $absoluteUrl);
        }
    }

    /**
     * @throws JsonException
     * @throws HttpException
     */
    public function requestBody(string $method, string $absoluteUrl, array $params = []): array
    {
        try {
            $res = $this->guzzleRequest($method, $absoluteUrl, $params)->getBody();
            return json_decode($res, true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw $this->createException($e, $absoluteUrl);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function guzzleRequest(string $method, string $absoluteUrl, array $params = []): ResponseInterface
    {
        return $this->client->request($method, $absoluteUrl, [
            'query' => $params['query'] ?? null,
            'json' => $params['body'] ?? null,
        ]);
    }

    private function createException(GuzzleException $e, string $url): HttpException
    {
        if ($e instanceof ClientException && $e->getCode() === 404) {
            return new HttpNotFoundException("Not found: $url!", 0, $e);
        }
        return new HttpException("Request failed: $url!", 0, $e);
    }

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 5,
        ]);
    }
}