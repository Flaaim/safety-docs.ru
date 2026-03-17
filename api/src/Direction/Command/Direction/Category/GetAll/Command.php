<?php

namespace App\Direction\Command\Direction\Category\GetAll;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        public string $directionId,
    ){
    }
}