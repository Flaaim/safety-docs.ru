<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetNewsletter;

final class Newsletter
{
    public function __construct(
        public string $newsletterId,
        public string $templateId,
        public string $subject,
        public string $status,
        public string $projectId,
        public string $createdAt,
    ) {
    }
}
