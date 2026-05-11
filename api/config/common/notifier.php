<?php

declare(strict_types=1);

use App\Shared\Domain\Service\Notification\TelegramNotifier;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    TelegramNotifier::class => function (ContainerInterface $container) {
        $config = $container->get('config')['telegramBot'];
        $client = new Client(['base_uri' => 'https://api.telegram.org/']);
        $logger = $container->get(LoggerInterface::class);
        return new TelegramNotifier($client, $logger, $config['token'], $config['chatId']);
    },
];
