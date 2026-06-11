<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project;

use App\Distribution\Entity\Project\DTO\ContactDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'distribution_projects')]
final class Project
{
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'project', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $contacts;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'project_id')]
        private ProjectId $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
    ) {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ProjectId
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getContacts(): array
    {
        return $this->contacts->toArray();
    }

    public function import(array $contacts): void
    {
        foreach ($contacts as $newContact) {
            if (!$newContact instanceof ContactDTO) {
                throw new \DomainException('Importing contacts must be an instance of ContactDTO');
            }
            if ($this->hasContact($newContact->email, $this->getId())) {
                continue;
            }
            $contact = new Contact(Uuid::uuid4()->toString(), $newContact->email, $this);
            $this->contacts->add($contact);
        }
    }
    public function hasContact(string $email, ProjectId $projectId): bool
    {
        foreach ($this->contacts as $existingContact) {
            /** @var Contact $existingContact */
            if ($existingContact->isEquals($email) && $existingContact->getProject()->getId()->equals($projectId)) {
                return true;
            }
        }
        return false;
    }

    public function unsubscribeContact(Contact $contact): void
    {
        foreach ($this->contacts as $existingContact) {
            if ($this->hasContact($contact->getEmail(), $contact->getProject()->getId())) {
                $existingContact->unsubscribe();
                return;
            }
        }
        throw new \DomainException('Contact not found in this distribution.');
    }
}
