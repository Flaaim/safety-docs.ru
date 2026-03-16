<?php

namespace App\Direction\Command\Category\Add;

class Command
{
    public function __construct(
        public string $directionId,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
    ){
    }
}