<?php

namespace App\Direction\Command\Direction\Category\Update;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $categoryId,
        #[Assert\Length(min: 1, max: 150)]
        public string $title,
        #[Assert\Length(min: 1, max: 255)]
        public string $description,
        #[Assert\NotBlank]
        public string $text,
        #[Assert\Length(min: 1, max: 35)]
        public string $slug,
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $directionId,
    ){
    }
}