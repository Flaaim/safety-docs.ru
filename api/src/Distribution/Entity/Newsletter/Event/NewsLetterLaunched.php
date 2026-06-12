<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter\Event;

final class NewsLetterLaunched
{
    public function __construct(
        public string $newsletterId,
    ) {}
}