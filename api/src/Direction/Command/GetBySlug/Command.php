<?php

namespace App\Direction\Command\GetBySlug;

use Symfony\Component\Validator\Constraints as Assert;
class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public string $slug,
    ){
    }
}