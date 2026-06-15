<?php

declare(strict_types=1);

namespace App\Distribution\MessageHandler;

use App\Distribution\Entity\Newsletter\Event\NewsLetterLaunched;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Distribution\Message\SendNewsletterBatch;
use App\Distribution\Service\NewsletterLauncherInterface;
use App\Flusher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class NewsletterLauncherHandler
{
    private const BATCH_SIZE = 100;
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly ProjectRepository $projects,
        private readonly Flusher $flusher,
        private readonly MessageBusInterface $messageBus,
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
        $emails = [];
        foreach ($subscribers as $subscriber) {
            $emails[] = $subscriber->getEmail();
        }

        $batches = array_chunk($emails, self::BATCH_SIZE);

        foreach ($batches as $batch) {
            $this->messageBus->dispatch(new SendNewsletterBatch(
                $newsletter->getId()->getValue(),
                $batch,
                $newsletter->getTemplateId(),
                $newsletter->getSubject()
            ));
        }

        $newsletter->completed();

        $this->flusher->flush();
    }
}
