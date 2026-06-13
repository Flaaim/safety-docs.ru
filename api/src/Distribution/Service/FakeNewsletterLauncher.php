<?php

declare(strict_types=1);

namespace App\Distribution\Service;

final class FakeNewsletterLauncher implements NewsletterLauncherInterface
{
    public array $sentBatches = [];
    public function launch(array $contacts, string $templateId, string $subject): void
    {
        $this->sentBatches[] = [
            'contacts' => $contacts,
            'templateId' => $templateId,
            'subject' => $subject,
        ];
    }

    public function clear(): void
    {
        $this->sentBatches = [];
    }
}
