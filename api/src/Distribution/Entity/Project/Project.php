<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project;

use Doctrine\Common\Collections\Collection;

final class Project
{
    public function __construct(
        private ProjectId  $id,
        private string     $name,
        private Collection $contacts,
    )
    {}

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
            if($this->hasContact($newContact)) {
                continue;
            }
            $this->contacts->add($newContact);
        }
    }
    public function hasContact(Contact $newContact): bool
    {
        foreach ($this->contacts as $existingContact) {
            /** @var Contact $existingContact */
            if($existingContact->isEquals($newContact)) {
                return true;
            }
        }
        return false;
    }

    public function unsubscribeContact(Contact $contact): void
    {
        foreach ($this->contacts as $existingContact) {
            if($existingContact->isEquals($contact)) {
                $existingContact->unsubscribe();
                return;
            }
        }
        throw new \DomainException('Contact not found in this distribution.');
    }
}
