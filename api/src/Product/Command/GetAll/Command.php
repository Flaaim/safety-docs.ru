<?php

namespace App\Product\Command\GetAll;

class Command
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }
}
