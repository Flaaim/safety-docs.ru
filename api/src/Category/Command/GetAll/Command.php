<?php

namespace App\Category\Command\GetAll;

class Command
{
    public function __construct(
        public string $directionId,
    ){
    }
}