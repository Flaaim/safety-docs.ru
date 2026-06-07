<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://9198a832ad0371b66d65eb292a025f5e@o4511418890452992.ingest.de.sentry.io/4511523894001744',
]);

$container = require __DIR__ . '/../config/container.php';

$commands = $container->get('config')['console']['commands'];

$app = new Application();

foreach ($commands as $command) {
    $app->add($container->get($command));
}



$app->run();
