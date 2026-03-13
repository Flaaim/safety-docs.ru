<?php

namespace App\Direction\Command\GetBySlug;

class Command
{
    public function __construct(
        public string $slug,
    ){
    }
}