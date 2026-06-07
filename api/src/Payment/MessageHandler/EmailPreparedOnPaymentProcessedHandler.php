<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Event\PaymentProcessed;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Sender\Event\SendDocumentEmailCommand;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class EmailPreparedOnPaymentProcessedHandler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ProductRepository $products,
        private readonly FileSystemPathInterface $fileSystemPath,
    ) {
    }

    public function __invoke(PaymentProcessed $event): void
    {
        $productId = $event->productId;
        $email = $event->email;

        $product = $this->products->get(new ProductId($productId));
        $pathToFile = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $product->getId()->getValue() .
            DIRECTORY_SEPARATOR . $product->getFilename()->getValue();

        $sendDocument = new SendDocumentEmailCommand($email, $product->getName(), $pathToFile);

        $this->messageBus->dispatch($sendDocument);
    }
}
