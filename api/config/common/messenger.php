<?php

declare(strict_types=1);

use App\Distribution\Entity\Newsletter\Event\NewsLetterLaunched;
use App\Distribution\Entity\Newsletter\Event\SendNewsletterBatch;
use App\Distribution\MessageHandler\NewsletterLauncherHandler;
use App\Distribution\MessageHandler\SendNewsletterBatchHandler;
use App\Parser\Event\ProcessedSingleDocument;
use App\Parser\MessageHandler\ProcessedSingleDocumentHandler;
use App\Payment\Event\PaymentProcessed;
use App\Payment\MessageHandler\EmailPreparedOnPaymentProcessedHandler;
use App\Sender\Event\SendDocumentEmailCommand;
use App\Sender\MessageHandler\SendDocumentOnEmailPreparedHandler;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisTransportFactory;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\Command\FailedMessagesRetryCommand;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

return [
    MessageBusInterface::class => function (ContainerInterface $container): MessageBus {
        $handlers = [
            PaymentProcessed::class => [
                new HandlerDescriptor(function (PaymentProcessed $event) use ($container) {
                    $handler = $container->get(EmailPreparedOnPaymentProcessedHandler::class);
                    return $handler($event);
                })
            ],
            SendDocumentEmailCommand::class => [
                new HandlerDescriptor(function (SendDocumentEmailCommand $event) use ($container) {
                    $handler = $container->get(SendDocumentOnEmailPreparedHandler::class);
                    return $handler($event);
                })
            ],
            NewsLetterLaunched::class => [
                new HandlerDescriptor(function (NewsLetterLaunched $event) use ($container) {
                    $handler = $container->get(NewsletterLauncherHandler::class);
                    return $handler($event);
                })
            ],
            SendNewsletterBatch::class => [
                new HandlerDescriptor(function (SendNewsletterBatch $event) use ($container) {
                    $handler = $container->get(SendNewsletterBatchHandler::class);
                    return $handler($event);
                })
            ],
            ProcessedSingleDocument::class => [
                new HandlerDescriptor(function (ProcessedSingleDocument $event) use ($container) {
                    $handler = $container->get(ProcessedSingleDocumentHandler::class);
                    return $handler($event);
                })
            ]
        ];
        $isTestEnv = getenv('APP_ENV') === 'test';
        $useAsync = !$isTestEnv && !empty(getenv('MESSENGER_TRANSPORT_DSN'));

        $sendersLocator = new SendersLocator([
            '*' => $useAsync ? [TransportInterface::class] : [],
        ], $container);

        return new MessageBus([
            new SendMessageMiddleware($sendersLocator),
            new HandleMessageMiddleware(new HandlersLocator($handlers)),
        ]);
    },
    'async' => DI\get(TransportInterface::class),
    'failed' => DI\get('messenger.transport.failed'),
    RoutableMessageBus::class => function (ContainerInterface $container): RoutableMessageBus {
        return new RoutableMessageBus(
            $container,
            $container->get(MessageBusInterface::class)
        );
    },
    'messenger.failure_transports.locator' => function (ContainerInterface $container): ServiceProviderInterface {
        return new class ($container) implements ServiceProviderInterface {
            public function __construct(private ContainerInterface $container)
            {
            }
            public function get(string $id): mixed
            {
                return $this->container->get($id);
            }
            public function has(string $id): bool
            {
                return $this->container->has($id);
            }
            public function getProvidedServices(): array
            {
                // Указываем, что сервис 'failed' возвращает TransportInterface
                return ['failed' => TransportInterface::class];
            }
        };
    },
    ConsumeMessagesCommand::class => function (ContainerInterface $container): ConsumeMessagesCommand {
        return new ConsumeMessagesCommand(
            $container->get(RoutableMessageBus::class),
            $container,
            $container->get(Symfony\Component\EventDispatcher\EventDispatcherInterface::class),
            $container->get(LoggerInterface::class),
        );
    },
    FailedMessagesShowCommand::class => function (ContainerInterface $container): FailedMessagesShowCommand {
        return new FailedMessagesShowCommand(
            'failed',
            $container->get('messenger.failure_transports.locator'),
        );
    },
    EventDispatcherInterface::class => function (ContainerInterface $container): EventDispatcherInterface {
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener(
            WorkerMessageFailedEvent::class,
            [
                new SendFailedMessageToFailureTransportListener(
                    $container->get('messenger.failure_transports.locator'),
                    $container->get(LoggerInterface::class),
                    ['async' => 'failed']
                ),
                'onMessageFailed'
            ]
        );

        $dispatcher->addListener(
            WorkerMessageFailedEvent::class,
            function (WorkerMessageFailedEvent $event) {
                $throwable = $event->getThrowable();

                \Sentry\captureException($throwable);
            }
        );
        return $dispatcher;
    },
    FailedMessagesRetryCommand::class => function (ContainerInterface $container): FailedMessagesRetryCommand {
        return new FailedMessagesRetryCommand(
            'failed',
            $container->get('messenger.failure_transports.locator'),
            $container->get(RoutableMessageBus::class),
            $container->get(EventDispatcherInterface::class),
            $container->get(LoggerInterface::class)
        );
    },
    TransportInterface::class => function (ContainerInterface $container): TransportInterface {
        $dsn = $container->get('config')['messenger']['async'];

        $factory = new RedisTransportFactory();
        return $factory->createTransport($dsn, [], new PhpSerializer());
    },
    'messenger.transport.failed' => function (ContainerInterface $container): TransportInterface {
        $dsn = $container->get('config')['messenger']['async'];

        $factory = new RedisTransportFactory();
        return $factory->createTransport($dsn, ['stream' => 'failed'], new PhpSerializer());
    },
    'config' => [
        'messenger' => [
            'async' => getenv('MESSENGER_TRANSPORT_DSN'),
        ],
    ],
];
