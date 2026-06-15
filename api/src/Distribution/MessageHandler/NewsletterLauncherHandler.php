<?php

declare(strict_types=1);

namespace App\Distribution\MessageHandler;

use App\Distribution\Entity\Newsletter\Event\NewsLetterLaunched;
use App\Distribution\Entity\Newsletter\Event\SendNewsletterBatch;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Query\GetSubscribedEmails\Fetcher;
use App\Distribution\Query\GetSubscribedEmails\Query;
use App\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class NewsletterLauncherHandler
{
    private const BATCH_SIZE = 100;
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly Fetcher $fetcher,
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
        $subscribedEmailsGenerator = $this->fetcher->fetch(new Query($newsletter->getProjectId()->getValue()));

        $emails = iterator_to_array($subscribedEmailsGenerator);

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
