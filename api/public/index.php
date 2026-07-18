<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;


require __DIR__ . '/../vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://0f744193cb82499f8710e89dc555c57c@o4510080200998912.ingest.de.sentry.io/4511753314566224',
    'ignore_exceptions' => [
        HttpNotFoundException::class,
        HttpMethodNotAllowedException::class,
        HttpForbiddenException::class,
        HttpBadRequestException::class,
    ]
]);

/** @var  ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';
/** @var App $app */
$app = (require __DIR__ . '/../config/app.php')($container);

$app->run();