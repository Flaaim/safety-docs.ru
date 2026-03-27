<?php

namespace App\Direction\Command\Direction\Category\GetBySlug;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public string $slug,
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $directionId
    ) {
    }
}