<?php

namespace App\Direction\Command\Direction\Category\GetAllByDirection;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $directionId,
    ){
    }
}