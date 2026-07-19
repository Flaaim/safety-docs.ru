<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return [
    ClientInterface::class => function () {
        return new Client([
            'base_uri' => 'https://1otruda.ru']);
    }
];
