<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project\DTO;

final class ContactDTO
{
    public function __construct(
        public string $email,
        public bool $isUnsubscribed = false,
    ) {
    }
}
