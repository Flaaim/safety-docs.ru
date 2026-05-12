<?php

namespace App\Direction\Command\Direction\Category\Delete;

class Command
{
    public function __construct(
        public string $categoryId
    ){
    }
}