<?php

namespace App\Direction\Command\Direction\Add;

class Command
{
    public function __construct(
        public string $title,
        public string $description,
        public string $text,
        public string $slug
    ){
    }
}