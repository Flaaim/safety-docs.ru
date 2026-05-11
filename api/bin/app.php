<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../config/container.php';

$commands = $container->get('config')['console']['commands'];

$app = new Application();

foreach ($commands as $command) {
    $app->add($container->get($command));
}



$app->run();
