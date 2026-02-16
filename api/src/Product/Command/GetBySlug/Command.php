<?php

namespace App\Product\Command\GetBySlug;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $slug,
    ){
    }
}