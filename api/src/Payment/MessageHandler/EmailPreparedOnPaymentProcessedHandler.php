<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Event\PaymentProcessed;
use App\Sender\Event\SendDocumentEmailCommand;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\DocumentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class EmailPreparedOnPaymentProcessedHandler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly DocumentRepository $documents,
        private readonly FileSystemPathInterface $fileSystemPath,
    ) {
    }

    public function __invoke(PaymentProcessed $event): void
    {
        $documentId = $event->documentId;
        $email = $event->email;

        $document = $this->documents->get(new DocumentId($documentId));
        $pathToFile = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $document->getId()->getValue() .
            DIRECTORY_SEPARATOR . $document->getFilename()->getValue();

        $sendDocument = new SendDocumentEmailCommand($email, $document->getName(), $pathToFile);

        $this->messageBus->dispatch($sendDocument);
    }
}
