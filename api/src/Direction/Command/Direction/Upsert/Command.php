<?php

namespace App\Direction\Command\Direction\Upsert;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        public string $directionId,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
    ){}
}