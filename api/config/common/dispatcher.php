<?php

declare(strict_types=1);

use App\Product\Entity\ProductRepository;
use App\Sender\Service\Message\CreatorInterface;
use App\Shared\Domain\Event\Payment\PaymentSubscriber;
use App\Shared\Domain\Service\Notification\TelegramNotifier;
use App\Shared\Domain\Service\Template\RootPath;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

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
        $em = $container->get(EntityManagerInterface::class);
        $mailer = $container->get(MailerInterface::class);
        $logger = $container->get(LoggerInterface::class);
        $creator = $container->get(CreatorInterface::class);


        return new PaymentSubscriber(
            $container->get(TelegramNotifier::class),
            $logger,
            new \App\Sender\Command\Send\Handler($mailer, $logger, $creator),
            new ProductRepository($em),
            $container->get(RootPath::class),
        );
    }
];