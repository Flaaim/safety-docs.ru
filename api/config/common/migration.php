<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    DependencyFactory::class => function (ContainerInterface $container): DependencyFactory {
        $entityManager = $container->get(EntityManagerInterface::class);
        $connection = $entityManager->getConnection();

        $configuration = new Configuration($connection);
        $configuration->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../../src/Data/Migration');

        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName('doctrine_migration_versions');
        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        return DependencyFactory::fromEntityManager(
            new ExistingConfiguration($configuration),
            new ExistingEntityManager($entityManager),
        );
    },
    DiffCommand::class => function (ContainerInterface $container) {
        return new DiffCommand($container->get(DependencyFactory::class));
    },
    MigrateCommand::class => function (ContainerInterface $container) {
        return new MigrateCommand($container->get(DependencyFactory::class));
    },
    ExecuteCommand::class => function (ContainerInterface $container) {
        return new ExecuteCommand($container->get(DependencyFactory::class));
    },
    GenerateCommand::class => function (ContainerInterface $container) {
        return new GenerateCommand($container->get(DependencyFactory::class));
    },
    LatestCommand::class => function (ContainerInterface $container) {
        return new LatestCommand($container->get(DependencyFactory::class));
    },
    StatusCommand::class => function (ContainerInterface $container) {
        return new StatusCommand($container->get(DependencyFactory::class));
    },
    UpToDateCommand::class => function (ContainerInterface $container) {
        return new UpToDateCommand($container->get(DependencyFactory::class));
    }
];
