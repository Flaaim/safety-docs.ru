<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use App\Distribution\Entity\Project\ProjectId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'newsletters')]
final class Newsletter
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'newsletter_id')]
        private NewsletterId $newsletterId,
        #[ORM\Column(type: 'string', length: 255)]
        private string $subject,
        #[ORM\Column(type: 'string', length: 255)]
        private string $templateId,
        private Status $status,
        #[ORM\Column(type: 'project_id')]
        private ProjectId $projectId,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $createdAt
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
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function completed(): void
    {
        $this->status = Status::completed();
    }
    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }
}
