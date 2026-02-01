<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;

return [
    EntityManagerInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            null
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        $connection = DriverManager::getConnection(
            $settings['connection'],
            $config
        );

        return new EntityManager($connection, $config);
    },
    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_mysql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'utf8mb4',
                'driverOptions' => [
                    1002 => "SET NAMES 'utf8mb4'",
                ]
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Payment/Entity',
                __DIR__ . '/../../src/Product/Entity',
            ],
            'types' => [
                App\Shared\Domain\ValueObject\IdType::NAME => App\Shared\Domain\ValueObject\IdType::class,
                App\Shared\Domain\ValueObject\UpdatedAtType::NAME => App\Shared\Domain\ValueObject\UpdatedAtType::class,


                App\Product\Entity\PriceType::NAME => App\Product\Entity\PriceType::class,
                App\Product\Entity\FileType::NAME => App\Product\Entity\FileType::class,


                App\Payment\Entity\EmailType::NAME => App\Payment\Entity\EmailType::class,
                App\Payment\Entity\StatusType::NAME => App\Payment\Entity\StatusType::class,

            ]
        ]
    ],
    EntityManagerProvider::class => function (ContainerInterface $container) {
        return new SingleManagerProvider($container->get(EntityManagerInterface::class));
    }
];