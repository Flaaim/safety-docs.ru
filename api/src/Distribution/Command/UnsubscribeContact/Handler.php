<?php

declare(strict_types=1);

namespace App\Distribution\Command\UnsubscribeContact;

use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $unsubscribedEmails = $command->emails;
        $project = $this->projects->findById(new ProjectId($command->projectId));

        if ($project === null) {
            throw new \DomainException('Project not found.');
        }
        foreach ($unsubscribedEmails as $email) {
            $project->unsubscribeContact($email);
        }

        $this->flusher->flush();
    }
}
