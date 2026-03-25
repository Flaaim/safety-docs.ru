<?php

namespace App\Direction\Command\Direction\Delete;

use Symfony\Component\Validator\Constraints as Assert;
class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $directionId
    ){
    }
}