<?php

declare(strict_types=1);

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

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
$container = require __DIR__ . '/../config/container.php';

$bus = $container->get(MessageBusInterface::class);

$receiver = $container->get(TransportInterface::class);

$eventDispatcher = new EventDispatcher();

$worker = new Worker(
    ['async' => $receiver],
    $bus,
    $eventDispatcher
);

echo "[*] Worker started. Waiting for messages in Redis...\n";

$worker->run();
