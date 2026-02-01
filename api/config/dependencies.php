<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$envValue = getenv('APP_ENV');

if(empty($envValue)){
    $envDir = __DIR__ . '/env';
    $file = $envDir . '/.env';
    if (file_exists($file)) {
        $dotenv = Dotenv::createUnsafeImmutable($envDir);
        $dotenv->load();
    } else {
        throw new RuntimeException('.env file not found at: ' . $file);
    }
}

$aggregator = new ConfigAggregator([
    new PhpFileProvider(__DIR__ . '/common/*.php'),
    new PhpFileProvider(__DIR__ . '/' . (getenv('APP_ENV') ?: 'prod') . '/*.php'),
]);

return $aggregator->getMergedConfig();