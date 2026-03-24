<?php


declare(strict_types=1);

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use Psr\Container\ContainerInterface;

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'login' => getenv('AUTH_LOGIN'),
        'password' => getenv('AUTH_PASSWORD'),
        'template_paths' => __DIR__ . '/../../public/templates',
    ],
    FileSystemPath::class => function (ContainerInterface $container) {
        return new FileSystemPath(
            $container->get('config')['template_paths'],
        );
    },

];