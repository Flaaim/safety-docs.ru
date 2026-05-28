<?php

declare(strict_types=1);

namespace App\Sender\Event;

final class SendDocumentEmailCommand
{
    public function __construct(
        public string $email,
        public string $subject,
        public string $pathToFile,
    ) {}
}