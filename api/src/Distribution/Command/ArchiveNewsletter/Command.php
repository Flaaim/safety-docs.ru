<?php

declare(strict_types=1);

namespace App\Distribution\Command\ArchiveNewsletter;

final class Command
{
    public function __construct(
        public readonly string $newsletterId,
    )
    {}
}