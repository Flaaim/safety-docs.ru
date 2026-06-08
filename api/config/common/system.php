<?php

declare(strict_types=1);

use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\FileSystem\DistributionSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\ImageSystemPath;
use Psr\Container\ContainerInterface;

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'login' => getenv('AUTH_LOGIN'),
        'password' => getenv('AUTH_PASSWORD'),
        'template_paths' => __DIR__ . '/../../public/templates',
        'images_paths' => __DIR__ . '/../../public/images',
        'distribution_import_files' => __DIR__ . '/../../public/distributions/import',
    ],
    FileSystemPath::class => function (ContainerInterface $container) {
        return new FileSystemPath(
            $container->get('config')['template_paths'],
        );
    },
    ImageSystemPath::class => function (ContainerInterface $container) {
        return new FileSystemPath(
            $container->get('config')['images_paths'],
        );
    },
    DistributionSystemPath::class => function (ContainerInterface $container) {
        return new FileSystemPath(
            $container->get('config')['distribution_import_files'],
        );
    }

];
