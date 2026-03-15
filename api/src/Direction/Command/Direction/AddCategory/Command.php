<?php

namespace App\Direction\Command\Direction\AddCategory;

class Command
{
    public function __construct(
        public string $directionId,
        public string $categoryId,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
    ){}
}