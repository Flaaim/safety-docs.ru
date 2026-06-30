<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
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
            true,
            $settings['proxy_dir'],
            null
        );

        $config->setSchemaAssetsFilter(function ($assetName) {
            return !str_starts_with($assetName, 'doctrine_migration_versions');
        });

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
    Connection::class => function (ContainerInterface $container): Connection {
        $em = $container->get(EntityManagerInterface::class);
        return $em->getConnection();
    },
    'config' => [
        'doctrine' => [
            'dev_mode' => (bool)getenv('APP_DEBUG'),
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
                __DIR__ . '/../../src/Sender/Entity',
                __DIR__ . '/../../src/Template/Entity',
                __DIR__ . '/../../src/Distribution/Entity',
            ],
            'types' => [
                App\Shared\Domain\ValueObject\IdType::NAME => App\Shared\Domain\ValueObject\IdType::class,
                App\Shared\Domain\ValueObject\UpdatedAtType::NAME => App\Shared\Domain\ValueObject\UpdatedAtType::class,


                App\Product\Entity\AmountType::NAME => App\Product\Entity\AmountType::class,
                App\Product\Entity\FilenameType::NAME => App\Product\Entity\FilenameType::class,
                App\Product\Entity\ProductIdType::NAME => App\Product\Entity\ProductIdType::class,

                App\Payment\Entity\EmailType::NAME => App\Payment\Entity\EmailType::class,
                App\Payment\Entity\PaymentStatusType::NAME => App\Payment\Entity\PaymentStatusType::class,
                App\Payment\Entity\PriceType::NAME => App\Payment\Entity\PriceType::class,

                App\Sender\Entity\MessageIdType::NAME => App\Sender\Entity\MessageIdType::class,
                App\Sender\Entity\MessageStatusType::NAME => App\Sender\Entity\MessageStatusType::class,
                App\Sender\Entity\EmailMessageType::NAME => App\Sender\Entity\EmailMessageType::class,


                App\Template\Entity\Direction\DirectionIdType::NAME => App\Template\Entity\Direction\DirectionIdType::class,
                App\Template\Entity\Direction\SlugType::NAME => App\Template\Entity\Direction\SlugType::class,

                App\Template\Entity\Category\CategoryIdType::NAME => App\Template\Entity\Category\CategoryIdType::class,
                App\Template\Entity\Category\SlugType::NAME => App\Template\Entity\Category\SlugType::class,

                App\Distribution\Entity\File\FileIdType::NAME => App\Distribution\Entity\File\FileIdType::class,
                App\Distribution\Entity\Project\ProjectIdType::NAME => App\Distribution\Entity\Project\ProjectIdType::class,

                App\Distribution\Entity\Newsletter\NewsletterIdType::NAME => App\Distribution\Entity\Newsletter\NewsletterIdType::class,
                App\Distribution\Entity\Newsletter\StatusType::NAME => App\Distribution\Entity\Newsletter\StatusType::class,

                App\Template\Entity\Document\DocumentIdType::NAME => App\Template\Entity\Document\DocumentIdType::class,
                App\Template\Entity\Document\AmountType::NAME => App\Template\Entity\Document\AmountType::class,
                App\Template\Entity\Document\FilenameType::NAME => App\Template\Entity\Document\FilenameType::class,
            ]
        ]
    ],
    EntityManagerProvider::class => function (ContainerInterface $container) {
        return new SingleManagerProvider($container->get(EntityManagerInterface::class));
    }
];
