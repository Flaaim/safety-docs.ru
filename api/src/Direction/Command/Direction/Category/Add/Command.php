<?php

namespace App\Direction\Command\Direction\Category\Add;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $directionId,
        #[Assert\Length(min: 1, max: 150)]
        public string $title,
        #[Assert\Length(min: 1, max: 255)]
        public string $description,
        #[Assert\NotBlank]
        public string $text,
        #[Assert\Length(min: 1, max: 35)]
        public string $slug,
    ){
    }
}