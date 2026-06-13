<?php

declare(strict_types=1);

use App\Distribution\Service\NewLetterLauncher;
use App\Distribution\Service\NewsletterLauncherInterface;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    NewsletterLauncherInterface::class => function (ContainerInterface $container): NewsletterLauncherInterface {
        $client = new Client([
            'base_uri' => 'https://goapi.unisender.ru/ru/transactional/api/v1/',
        ]);

        $logger = $container->get(LoggerInterface::class);
        $apiKey = $container->get('config')['uniSender']['apiKey'];

        return new NewLetterLauncher($client, $logger, $apiKey);
    },
    'config' => [
        'uniSender' => [
            'apiKey' => getenv('UNI_SENDER_API'),
        ]
    ]
];
