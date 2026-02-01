<?php

declare(strict_types=1);

use App\Shared\Domain\Event\Payment\PaymentSubscriber;
use App\Shared\Domain\Service\Notification\TelegramNotifier;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

return [
    EventDispatcher::class => function (ContainerInterface $container) {
        $dispatcher = new EventDispatcher();

        $subscribers = $container->get('config')['event_subscribers'];
        foreach ($subscribers as $className) {
            $subscriber = $container->get($className);
            if ($subscriber instanceof EventSubscriberInterface) {
                $dispatcher->addSubscriber($subscriber);
            }
        }

        return $dispatcher;
    },
    'config' => [
        'event_subscribers' => [
            PaymentSubscriber::class,
        ]
    ],
    PaymentSubscriber::class => function (ContainerInterface $container) {
        return new PaymentSubscriber(
            $container->get(TelegramNotifier::class),
            $container->get(LoggerInterface::class)
        );
    }
];