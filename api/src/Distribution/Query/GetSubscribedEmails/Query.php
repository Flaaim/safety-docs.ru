<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetSubscribedEmails;

final class Query
{
    public function __construct(
        public readonly string $projectId
    ) {
    }
}
