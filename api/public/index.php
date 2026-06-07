<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://9198a832ad0371b66d65eb292a025f5e@o4511418890452992.ingest.de.sentry.io/4511523894001744',
]);

/** @var  ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';
/** @var App $app */
$app = (require __DIR__ . '/../config/app.php')($container);

$app->run();