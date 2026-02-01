<?php


declare(strict_types=1);


use App\Shared\Domain\Service\Template\TemplatePath;
use Psr\Container\ContainerInterface;

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'login' => getenv('AUTH_LOGIN'),
        'password' => getenv('AUTH_PASSWORD'),
        'template_paths' => __DIR__ . '/../../public/templates',
    ],
    TemplatePath::class => function (ContainerInterface $container) {
        return new TemplatePath(
            $container->get('config')['template_paths'],
        );
    },

];