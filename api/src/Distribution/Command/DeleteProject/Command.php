<?php

declare(strict_types=1);

namespace App\Distribution\Command\DeleteProject;

final class Command
{
    public function __construct(
       public string $projectId,
    ) {}
}