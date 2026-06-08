<?php

declare(strict_types=1);

namespace App\Distribution\Command\GetContactFiles;

use Symfony\Component\Validator\Constraints as Assert;
final class Command
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $page,
        #[Assert\GreaterThan(0)]
        public int $perPage,
    ) {}
}