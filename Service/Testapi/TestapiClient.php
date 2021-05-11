<?php

namespace Service\Testapi;

use Exception;
use Service\HttpClient;
use Service\HttpNotFoundException;

class TestapiClient
{
    private HttpClient $httpClient;
    private string $login;
    private string $pass;
    private string $baseUrl = 'http://testapi.ru';
    private ?string $token = null;

    /**
     * @throws TestapiException
     */
    public function getUser(string $username): UserDto
    {
        try {
            $body = $this->httpClient->requestBody('get', $this->getAbsoluteUrl("get-user/$username"), [
                'query' => ['token' => $this->getToken()]
            ]);
            $this->validateResBody($body);
            return UserDto::fromArray($body);
        } catch (Exception $e) {
            throw $this->createException("Failed to get user: $username!", $e);
        }
    }

    /**
     * @throws TestapiException
     */
    public function updateUser(UserDto $userDto): void
    {
        try {
            $body = $this->httpClient->requestbody('post', $this->getabsoluteurl( "user/{$userDto->id}/update"), [
                'query' => ['token' => $this->getToken()],
                'body' => $userDto->toArray()
            ]);
            $this->validateResBody($body);
        } catch (Exception $e) {
            throw $this->createException("Failed to update user: {$userDto->id}!", $e);
        }
    }

    /**
     * @throws TestapiException
     */
    private function getToken(): string
    {
        if ($this->token === null) {
            $this->token = $this->auth();
        }
        return $this->token;
    }

    /**
     * @throws TestapiException
     */
    public function auth(): string
    {
        try {
            $body = $this->httpClient->requestBody('get', $this->getAbsoluteUrl('auth'), [
                'query' => [
                    'login' => $this->login,
                    'pass' => $this->pass,
                ]
            ]);
            $this->validateResBody($body);
            return $body['token'];
        } catch (Exception $e) {
            throw $this->createException('Failed to get access token!', $e);
        }
    }

    /**
     * @throws TestapiException
     */
    private function validateResBody(array $body) {
        if ($body['status'] === 'OK') return;
        switch ($body['status']) {
            case 'Not found': throw new TestapiException('Test API resource not found!', TestapiException::CODE_NOT_FOUND);
            case 'Error': throw new TestapiException('Test API request failed!');
            default: throw new TestapiException('Test API response status is unsuccessful!');
        }
    }

    private function createException(string $message, Exception $previous): TestapiException
    {
        if ($previous instanceof TestapiException) {
            $code = $previous->getCode();
        } elseif ($previous instanceof HttpNotFoundException) {
            $code = TestapiException::CODE_NOT_FOUND;
        } else {
            $code = 0;
        }
        return new TestapiException($message,  $code, $previous);
    }

    private function getAbsoluteUrl(string $path): string
    {
        return $this->baseUrl . '/' . $path;
    }

    public function __construct(HttpClient $httpClient, string $login, string $pass, ?string $baseUrl = null)
    {
        $this->httpClient = $httpClient;
        $this->login = $login;
        $this->pass = $pass;
        $baseUrl === null || $this->baseUrl = $baseUrl;
    }
}