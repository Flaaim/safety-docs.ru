<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project\DTO;

use App\Distribution\Entity\Project\Project;

final class ProjectDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public array $contacts
    ) {
    }

    public static function fromProject(Project $project): self
    {
        return new self(
            $project->getId()->getValue(),
            $project->getName(),
            $project->getContacts()
        );
    }
}
