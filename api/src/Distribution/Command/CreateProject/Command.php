<?php

declare(strict_types=1);

namespace App\Distribution\Command\CreateProject;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name
    ) {
    }
}
