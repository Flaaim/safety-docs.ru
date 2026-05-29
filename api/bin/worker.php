<?php

declare(strict_types=1);

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

require __DIR__ . '/../vendor/autoload.php';
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