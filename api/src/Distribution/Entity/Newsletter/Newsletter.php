<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

final class Newsletter
{
    public function __construct(
        private NewsletterId $newsletterId,
        private string $subject,
        private string $templateId,
    ) {
    }

    public function getId(): NewsletterId
    {
        return $this->newsletterId;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function getTemplateId(): string
    {
        return $this->templateId;
    }
}
