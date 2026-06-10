<?php

declare(strict_types=1);

namespace App\Distribution\Command\ImportContacts;

final class Command
{
    public function __construct(
        public string $fileId,
        public string $projectId,
    ) {}
}