<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity;

use App\Distribution\Entity\Newsletter\Newsletter;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\Status;
use App\Distribution\Entity\Project\ProjectId;

final class NewsletterBuilder
{
    private NewsletterId $newsletterId;
    private string $subject;
    private string $templateId;
    private Status $status;
    private ProjectId $projectId;
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->newsletterId = NewsletterId::generate();
        $this->subject = 'Обновления на сайте';
        $this->templateId = 'd255c7a2-64e7-4cb0-b419-69a2340e61b5';
        $this->status = Status::created();
        $this->projectId = ProjectId::generate();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function withId(NewsletterId $newsletterId): self
    {
        $clone = clone $this;
        $clone->newsletterId = $newsletterId;
        return $clone;
    }

    public function withSubject(string $subject): self
    {
        $clone = clone $this;
        $clone->subject = $subject;
        return $clone;
    }

    public function withTemplateId(string $templateId): self
    {
        $clone = clone $this;
        $clone->templateId = $templateId;
        return $clone;
    }
    public function withStatus(Status $status): self
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }
    public function withProjectId(ProjectId $projectId): self
    {
        $clone = clone $this;
        $clone->projectId = $projectId;
        return $clone;
    }
    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $clone = clone $this;
        $clone->createdAt = $createdAt;
        return $clone;
    }
    public function build(): Newsletter
    {
        $newsletter = new Newsletter(
            $this->newsletterId,
            $this->subject,
            $this->templateId,
            $this->status,
            $this->projectId,
            $this->createdAt
        );

        return $newsletter;
    }
}
