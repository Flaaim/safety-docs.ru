<?php

declare(strict_types=1);

namespace App\Parser\Command\Launch;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $categoryId,
        #[Assert\GreaterThan(0)]
        public float $amount,
        #[Assert\NotBlank]
        public readonly string $url,
        #[Assert\NotBlank]
        public readonly string $cookie
    ) {
    }
}
