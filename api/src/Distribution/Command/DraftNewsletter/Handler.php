<?php

declare(strict_types=1);

namespace App\Distribution\Command\DraftNewsletter;

use App\Distribution\Entity\Newsletter\Newsletter;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Newsletter\Status;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly ProjectRepository $projects,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        if(!$this->projects->hasById(new ProjectId($command->projectId))){
            throw new \DomainException('Project not found.');
        }
        $newsletter = new Newsletter(
            NewsletterId::generate(),
            $command->subject,
            $command->templateId,
            Status::created(),
            new ProjectId($command->projectId),
        );

        $this->newsletters->add($newsletter);
        $this->flusher->flush();
    }
}