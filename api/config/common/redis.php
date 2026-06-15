<?php

declare(strict_types=1);

use App\Distribution\Service\DailyQuotaTracker;

return [
    \Redis::class => function (): \Redis {
        $redis = new \Redis();
        $host = getenv('REDIS_HOST') ?: 'redis';
        $redis->connect($host, 6379);
        return $redis;
    },
    DailyQuotaTracker::class => \DI\autowire(),
];
