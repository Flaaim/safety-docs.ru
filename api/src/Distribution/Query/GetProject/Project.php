<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetProject;

final class Project
{
    public function __construct(
        public string $projectId,
        public string $name,
        public array $contacts
    ) {
    }
}
