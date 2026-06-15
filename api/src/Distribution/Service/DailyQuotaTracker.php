<?php

declare(strict_types=1);

namespace App\Distribution\Service;

final class DailyQuotaTracker
{
    private const MAX_QUOTA = 1500;

    public function __construct(
        private readonly \Redis $redis
    ){}

    public function reserve(int $batchSize): bool
    {
        $timezone = new \DateTimeZone('Europe/Moscow');
        $now = new \DateTimeImmutable('now', $timezone);

        $key = 'unisender_quota_' . $now->format('Y-m-d');

        $currentUsage = $this->redis->get($key);

        if(($currentUsage + $batchSize) > self::MAX_QUOTA){
            return false;
        }

        $this->redis->incrBy($key, $batchSize);

        if ($this->redis->ttl($key) === -1) {
            $tomorrow = $now->modify('tomorrow');
            $secondsUntilMidnight = $tomorrow->getTimestamp() - $now->getTimestamp();
            $this->redis->expire($key, $secondsUntilMidnight);
        }

        return true;
    }
}