<?php

declare(strict_types=1);

namespace App\Distribution\MessageHandler;

use App\Distribution\Entity\Newsletter\Event\NewsLetterLaunched;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Distribution\Query\GetNewsletter\Fetcher;
use App\Distribution\Query\GetNewsletter\Query;
use App\Distribution\Service\NewsletterLauncherInterface;
use App\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NewsletterLauncherHandler
{
    private const BATCH_SIZE = 100;
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly ProjectRepository $projects,
        private readonly NewsletterLauncherInterface $launcher,
        private readonly Flusher $flusher,
    ) {
    }

    public function __invoke(NewsLetterLaunched $event): void
    {
        $newsletter = $this->newsletters->findById(new NewsletterId($event->newsletterId));
        if ($newsletter === null) {
            throw new \DomainException('Newsletter not found.');
        }
        $project = $this->projects->findById($newsletter->getProjectId());
        if ($project === null) {
            throw new \DomainException('Project not found.');
        }
        $subscribers = $project->getSubscribedContacts();

        $batch = [];
        foreach ($subscribers as $subscriber) {
            $batch[] = ['email' => $subscriber->getEmail()];

            if (count($batch) >= self::BATCH_SIZE) {
                $this->launcher->launch($batch, $newsletter->getTemplateId(), $newsletter->getSubject());
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            $this->launcher->launch($batch, $newsletter->getTemplateId(), $newsletter->getSubject());
        }

        $newsletter->completed();

        $this->flusher->flush();
    }
}
