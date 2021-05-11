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
            'https://eis3skveaa.api.quickmocker.com',
        );
    }

    public static function createHttpClient(): HttpClient
    {
        return new GuzzleHttpClient();
    }
}