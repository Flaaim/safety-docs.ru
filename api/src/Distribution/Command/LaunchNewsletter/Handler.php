<?php

declare(strict_types=1);

namespace App\Distribution\Command\LaunchNewsletter;

use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Distribution\Service\NewsletterLauncherInterface;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly NewsletterRepository        $newsletters,
        private readonly ProjectRepository           $projects,
        private readonly NewsletterLauncherInterface $launcher,
        private readonly Flusher                     $flusher
    ) {}

    public function handle(Command $command): void
    {
        $newsletter = $this->newsletters->findById(new NewsletterId($command->newsletterId));
        if($newsletter === null) {
            throw new \DomainException('Newsletter not found.');
        }
        $project = $this->projects->findById($newsletter->getProjectId());
        if($project === null) {
            throw new \DomainException('Project not found.');
        }

        $subscribedContacts = $project->getSubscribedContacts();

        $newsletter->launched();

        $this->launcher->launch($subscribedContacts, $newsletter->getTemplateId(), $newsletter->getSubject());

        $this->flusher->flush();
    }

}