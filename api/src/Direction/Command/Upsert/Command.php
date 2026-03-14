<?php

namespace App\Direction\Command\Upsert;

use App\Direction\Entity\DirectionRepository;

class Command
{
    public function __construct(
        public string $directionId,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
    ){}
}