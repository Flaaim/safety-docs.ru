<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetProject;

final class Query
{
    public function __construct(
        public string $projectId
    ) {
    }
}
