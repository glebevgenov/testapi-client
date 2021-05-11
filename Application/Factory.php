<?php

namespace Application;

use Service\HttpClient;
use Service\Testapi\TestapiClient;

class Factory
{
    public static function createTestapiClient(): TestapiClient
    {
        return new TestapiClient(
            self::createHttpClient(),
            'test',
            '12345',
            'http://testapi.ru',
        );
    }

    public static function createHttpClient(): HttpClient
    {
        return new GuzzleHttpClient();
    }
}