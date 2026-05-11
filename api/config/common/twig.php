<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

return [
  Environment::class => function (ContainerInterface $container) {
    $config = $container->get('config')['twig'];

    $loader = new FilesystemLoader();
    foreach ($config['template_dirs'] as $alias => $dir) {
        $loader->addPath($dir, $alias);
    }

    $environment = new Environment($loader, [
        'cache' => $config['debug'] ? false : $config['cache_dir'],
        'debug' => $config['debug'],
        'strict_variables' => $config['debug'],
        'auto_reload' => $config['debug'],
    ]);

    if ($config['debug']) {
        $environment->addExtension(new DebugExtension());
    }

    foreach ($config['globals'] as $name => $value) {
        $environment->addGlobal($name, $value);
    }

    foreach ($config['extensions'] as $class) {
        /** @var ExtensionInterface $extension */
        $extension = $container->get($class);
        $environment->addExtension($extension);
    }
    return $environment;
  },
    'config' => [
        'twig' => [
            'debug' => getenv('APP_DEBUG'),
            'template_dirs' => [
                FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../../templates',
            ],
            'cache_dir' => __DIR__ . '/../../var/cache/twig',
            'extensions' => [],
            'globals' => [
                'site_url' => getenv('SITE_URL'),
                'site_name' => getenv('SITE_NAME'),
            ],
        ]
    ]
];
