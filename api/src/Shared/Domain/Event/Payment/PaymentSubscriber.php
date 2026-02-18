<?php

namespace App\Shared\Domain\Event\Payment;

use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Sender\Command\DeliverMessage\Create\Command;
use App\Sender\Command\DeliverMessage\Create\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Recipient;
use App\Shared\Domain\Service\Notification\TelegramNotifier;
use App\Shared\Domain\Service\Template\RootPath;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TelegramNotifier $notifier,
        private readonly LoggerInterface $logger,
        private readonly Handler $handler,
        private readonly ProductRepository $products,
        private readonly RootPath $rootPath,
    )
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            SuccessfulPaymentEvent::class => [
                ['onSuccessPayment', 10],
                ['logSuccessfulPayment', 0],
            ],
        ];
    }
    public function onSuccessPayment(SuccessfulPaymentEvent $event): void
    {
            $payment = $event->getPayment();

            $product = $this->products->get(new ProductId($payment->getProductId()));
            $file = $product->getFile();
            $file->mergeRoot($this->rootPath);

            $recipient = new Recipient(new EmailMessage($payment->getEmail()->getValue()), $product->getName());
            $recipient->addAttachment($file);


            $command = new Command($recipient);
            $this->handler->handle($command);
    }

    public function logSuccessfulPayment(SuccessfulPaymentEvent $event): void
    {
        $payment = $event->getPayment();
        $this->notifier->sendSuccessfulPayment($event);
        $this->logger->info('Successful Payment', ['payment' => [
            'email' => $payment->getEmail()->getValue(),
            'price' => $payment->getPrice()->getValue(),
            'productId' => $payment->getProductId()
        ]]);
    }
}