<?php

declare(strict_types=1);

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

require __DIR__ . '/../vendor/autoload.php';
\Sentry\init([
    'dsn' => 'https://9198a832ad0371b66d65eb292a025f5e@o4511418890452992.ingest.de.sentry.io/4511523894001744',
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
