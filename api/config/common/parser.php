<?php

declare(strict_types=1);

use App\Parser\Service\DocumentAiRewriter;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    ClientInterface::class => function () {
        return new Client();
    },
    DocumentAiRewriter::class => function (ContainerInterface $container) {
        $apiKey = $container->get('config')['AiRewriter'];
        return new DocumentAiRewriter(
            $container->get(ClientInterface::class),
            $container->get(LoggerInterface::class),
            $apiKey['apiKey']
        );
    },
    'config' => [
        'AiRewriter' => [
            'apiKey' => getenv('AI_REWRITER_API_KEY'),
        ]
    ]
];
