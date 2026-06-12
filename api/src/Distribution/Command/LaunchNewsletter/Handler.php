<?php

declare(strict_types=1);

namespace App\Distribution\Command\LaunchNewsletter;

use App\Distribution\Entity\Newsletter\Event\NewsLetterLaunched;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Project\Contact;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Distribution\Service\NewLetterLauncher;
use App\Distribution\Service\NewsletterLauncherInterface;
use App\Flusher;
use Symfony\Component\Messenger\MessageBusInterface;

final class Handler
{
    public function __construct(
        private readonly NewsletterRepository  $newsletters,
        private readonly ProjectRepository     $projects,
        private readonly Flusher               $flusher
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

        $newsletter->launch();

        $this->flusher->flush();
    }

}