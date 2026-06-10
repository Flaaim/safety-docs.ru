<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project;

use App\Distribution\Entity\Project\DTO\ContactDTO;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'project_contacts')]
final class Contact
{
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isUnsubscribed  = false;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private string $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $email,
        #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'contacts')]
        #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private Project $project
    ) {
        Assert::email($email);
        $this->email = mb_strtolower($email);
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getProject(): Project
    {
        return $this->project;
    }
    public function isEquals(string $email): bool
    {
        return $this->email === $email;
    }

    public function isUnsubscribed(): bool
    {
        return $this->isUnsubscribed;
    }
    public function unsubscribe(): void
    {
        $this->isUnsubscribed = true;
    }
}