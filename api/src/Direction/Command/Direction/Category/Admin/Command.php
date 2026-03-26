<?php

namespace App\Direction\Command\Direction\Category\Admin;

class Command
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
    ){}
}