<?php

namespace Service;

interface HttpClient
{
    public function request(string $method, string $absoluteUrl, array $params = []): void;
    public function requestBody(string $method, string $absoluteUrl, array $params = []): array;
}