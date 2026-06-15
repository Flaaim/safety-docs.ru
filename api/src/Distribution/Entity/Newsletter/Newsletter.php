<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use App\Distribution\Entity\Project\ProjectId;
use App\Shared\AggregateRoot;
use App\Shared\EventTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'newsletters')]
final class Newsletter implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'newsletter_id')]
        private NewsletterId $newsletterId,
        #[ORM\Column(type: 'string', length: 255)]
        private string $subject,
        #[ORM\Column(type: 'string', length: 255)]
        private string $templateId,
        #[ORM\Column(type: 'status')]
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
    public function launch(): void
    {
        if ($this->status->getValue() === NewsletterStatus::Processed->value) {
            throw new \DomainException('Newsletter is already processed.');
        }
        $this->status = Status::processed();

        $this->recordEvent(new Event\NewsLetterLaunched($this->getId()->getValue()));
    }

    public function archive(): void
    {
        if($this->status->getValue() === NewsletterStatus::Archived->value) {
            throw new \DomainException('Newsletter is already archived.');
        }
        $this->status = Status::archived();
    }
    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }
}
