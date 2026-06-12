<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\Collections\ArrayCollection;

final class ProjectBuilder
{
    private ProjectId $projectId;
    private string $name;

    private array $contacts;

    public function __construct(projectId $projectId, string $name)
    {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->contacts = [];
    }

    public function withId(ProjectId $id): self
    {
        $clone = $this;
        $clone->projectId = $id;
        return $clone;
    }

    public function withName(string $name): self
    {
        $clone = $this;
        $clone->name = $name;
        return $clone;
    }
    public function withContacts(array $contacts): self
    {
        $clone = $this;
        $clone->contacts = $contacts;
        return $clone;
    }

    public function build(): Project
    {
        $project =  new Project(
          $this->projectId,
          $this->name,
        );

        if(!empty($this->contacts)) {
            $project->import($this->contacts);
        }

        return $project;
    }
}