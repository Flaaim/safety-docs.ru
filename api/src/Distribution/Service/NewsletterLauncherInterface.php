<?php

declare(strict_types=1);

namespace App\Distribution\Service;

use App\Distribution\Entity\Project\DTO\ContactDTO;

interface NewsletterLauncherInterface
{
    /** @var array<ContactDTO> $contacts */
    public function launch(array $contacts, string $templateId, string $subject): void;
}