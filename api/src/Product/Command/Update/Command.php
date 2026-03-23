<?php

namespace App\Product\Command\Update;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $productId,
        #[Assert\Length(min: 5, max: 255)]
        public string $name,
        #[Assert\NotBlank]
        public string $cipher,
        #[Assert\Positive]
        public float $amount,
        #[Assert\NotBlank]
        public string $path,
        #[Assert\NotBlank]
        public string $slug,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'd.m.Y')]
        public string $updatedAt,
    ){}
}