<?php

declare(strict_types=1);

use App\Payment\Event\PaymentProcessed;
use App\Payment\MessageHandler\EmailPreparedOnPaymentProcessedHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Redis\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisTransport;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisTransportFactory;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\TransportInterface;

return [
    MessageBusInterface::class => function(ContainerInterface $container): MessageBus {
        $handlers = [
            PaymentProcessed::class => [
                new HandlerDescriptor($container->get(EmailPreparedOnPaymentProcessedHandler::class))
            ]
        ];

        $sendersLocator = new SendersLocator([
            '*' => getenv('MESSENGER_TRANSPORT_DSN') ? [TransportInterface::class] : [],
        ], $container);

        return new MessageBus([
            new SendMessageMiddleware($sendersLocator),
            new HandleMessageMiddleware(new HandlersLocator($handlers)),
        ]);
    },
    TransportInterface::class => function(ContainerInterface $container): TransportInterface {
        $dsn = $container->get('config')['messenger']['async'];

        $factory = new RedisTransportFactory();
        return $factory->createTransport($dsn, [], new PhpSerializer());
    },
    'config' => [
        'messenger' => [
            'async' => getenv('MESSENGER_TRANSPORT_DSN'),
        ],
    ],
];