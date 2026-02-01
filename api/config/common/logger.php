<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => function (ContainerInterface $container) {
        $config = $container->get('config')['logger'];
        $telegramConfig = $container->get('config')['telegramBot'];

        $level = $config['debug'] ? Level::Debug : Level::Info;

        $log = new Logger('payment-service');

        if(!empty($telegramConfig)){
            $log->pushHandler(new TelegramBotHandler(
                $telegramConfig['token'],
                $telegramConfig['chatId'],
                Level::Warning,
            ));
        }


        if ($config['stderr']) {
            $log->pushHandler(new StreamHandler('php://stderr', $level));
        }

        if (!empty($config['file'])) {
            $log->pushHandler(new StreamHandler($config['file'], $level));
        }
        return $log;
    },
    'config' => [
        'logger' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'file' => __DIR__ . '/../../var/log/' . PHP_SAPI . '/application.log',
            'stderr' => true,
        ]
    ]
];