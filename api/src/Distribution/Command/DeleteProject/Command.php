<?php

declare(strict_types=1);

namespace App\Distribution\Command\DeleteProject;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
       public string $projectId,
    ) {}
}