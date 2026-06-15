<?php

declare(strict_types=1);

namespace App\Distribution\MessageHandler;

use App\Distribution\Message\SendNewsletterBatch;
use App\Distribution\Service\DailyQuotaTracker;
use App\Distribution\Service\NewsletterLauncherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsMessageHandler]
final class SendNewsletterBatchHandler
{
    public function __construct(
        private readonly NewsletterLauncherInterface $launcher,
        private readonly DailyQuotaTracker $quotaTracker,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(SendNewsletterBatch $message): void
    {
        $batchSize = count($message->emails);

        if (!$this->quotaTracker->reserve($batchSize)) {
            $timezone = new \DateTimeZone('Europe/Moscow');
            $now = new \DateTimeImmutable('now', $timezone);
            $tomorrow = $now->modify('tomorrow');

            $delayMilliseconds = ($tomorrow->getTimestamp() - $now->getTimestamp()) * 1000;

            $this->messageBus->dispatch($message, [
                new DelayStamp($delayMilliseconds)
            ]);

            return;
        }

        $formattedContacts = array_map(fn(string $email) => ['email' => $email], $message->emails);

        try {
            $this->launcher->launch($formattedContacts, $message->templateId, $message->subject);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
