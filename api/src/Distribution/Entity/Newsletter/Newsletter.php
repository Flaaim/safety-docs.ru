<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use App\Distribution\Entity\Project\ProjectId;

final class Newsletter
{
    public function __construct(
        private NewsletterId $newsletterId,
        private string $subject,
        private string $templateId,
        private Status $status,
        private ProjectId $projectId
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function completed(): void
    {
        $this->status = Status::completed();
    }
}
