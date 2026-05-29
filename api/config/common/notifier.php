<?php

declare(strict_types=1);

use App\Shared\Domain\Service\Notification\MessageHandler\SendTelegramHandler;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    SendTelegramHandler::class => function (ContainerInterface $container) {
        $config = $container->get('config')['telegramBot'];
        $client = new Client(['base_uri' => 'https://api.telegram.org/']);
        $logger = $container->get(LoggerInterface::class);
        return new SendTelegramHandler($client, $logger, $config['token'], $config['chatId']);
    },
];
