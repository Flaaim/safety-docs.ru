<?php

namespace App\Direction\Command\Category\GetAll;

class Command
{
    public function __construct(
        public string $directionId,
    ){
    }
}