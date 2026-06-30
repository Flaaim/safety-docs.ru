<?php

declare(strict_types=1);

namespace App\Distribution\Command\LaunchNewsletter;

use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Flusher;
use Symfony\Component\Messenger\MessageBusInterface;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly ProjectRepository $projects,
        private readonly Flusher $flusher,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function handle(Command $command): void
    {
        $newsletter = $this->newsletters->findById(new NewsletterId($command->newsletterId));
        if ($newsletter === null) {
            throw new \DomainException('Newsletter not found.');
        }
        $project = $this->projects->findById($newsletter->getProjectId());
        if ($project === null) {
            throw new \DomainException('Project not found.');
        }

        $newsletter->launch();

        $events = $newsletter->releaseEvents();

        $this->flusher->flush();

        foreach ($events as $event) {
            $this->messageBus->dispatch($event);
        }
    }
}
