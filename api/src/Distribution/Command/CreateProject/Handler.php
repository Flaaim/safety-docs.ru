<?php

declare(strict_types=1);

namespace App\Distribution\Command\CreateProject;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Flusher;

final class Handler
{
    public function __construct(
        private ProjectRepository $projects,
        private Flusher $flusher
    ) {}
    public function handle(Command $command): void
    {
        if($this->projects->hasByName($command->name)){
            throw new \DomainException('Project with this name already exists.');
        }

        $project = new Project(
            ProjectId::generate(),
            $command->name,
        );

        $this->projects->add($project);

        $this->flusher->flush();
    }
}