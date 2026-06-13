<?php

declare(strict_types=1);

namespace App\Distribution\Command\UnsubscribeContact;

final class Command
{
    const PROJECT_ID = '067cc559-7fa7-4d9d-b747-9b0d7d3382e0';
    public function __construct(
        public array $emails,
        public string $projectId = self::PROJECT_ID
    ) {}
}