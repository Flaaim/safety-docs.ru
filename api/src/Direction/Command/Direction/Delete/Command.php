<?php

namespace App\Direction\Command\Direction\Delete;

class Command
{
    public function __construct(
        public string $directionId
    ){
    }
}