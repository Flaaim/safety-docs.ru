<?php

declare(strict_types=1);

use App\Command\FixturesLoadCommand;
use App\Command\ProductSendCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        $config = $container->get('config')['console'];

        /** @var EntityManagerInterface $em  $em */
        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand(
            $em,
            $config['fixture_paths']
        );
    },
    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,
                ValidateSchemaCommand::class,

                DiffCommand::class,
                ExecuteCommand::class,
                MigrateCommand::class,
                LatestCommand::class,
                StatusCommand::class,
                UpToDateCommand::class,

                ProductSendCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Product/Fixture',
                __DIR__ . '/../../src/Payment/Fixture',
            ]
        ],
    ]
];