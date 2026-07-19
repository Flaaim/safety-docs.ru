<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return [
    ClientInterface::class => function () {
        return new Client();
    }
];