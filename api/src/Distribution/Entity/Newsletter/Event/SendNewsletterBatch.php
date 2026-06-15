<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter\Event;

final class SendNewsletterBatch
{
    public function __construct(
        public readonly string $newsletterId,
        public readonly array $emails,
        public readonly string $templateId,
        public readonly string $subject,
    ) {
    }
}
