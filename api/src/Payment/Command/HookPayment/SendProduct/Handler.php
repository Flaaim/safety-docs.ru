<?php

namespace App\Payment\Command\HookPayment\SendProduct;

use App\Payment\Entity\Payment;
use App\Payment\Entity\Status;
use App\Payment\Service\Delivery\ProductDeliveryService;
use App\Shared\Domain\Event\Payment\SuccessfulPaymentEvent;
use App\Shared\Domain\Service\Payment\PaymentStatus;
use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Handler
{
    public function __construct(
        private readonly ProductDeliveryService $delivery,
        private readonly EventDispatcher $dispatcher
    )
    {}
    public function handle(Command $command): void
    {
        if(!$this->shouldSendProduct($command->payment, $command->paymentWebHookData)){
            return;
        }

        $this->delivery->deliver($command->paymentWebHookData);

        $command->payment->setSuccess(Status::succeeded());

        $event = new SuccessfulPaymentEvent($command->payment);

        $this->dispatcher->dispatch($event);
    }
    private function shouldSendProduct(Payment $payment, PaymentWebhookDataInterface $webhookData): bool
    {
        return !$payment->isSend() && $webhookData->isPaid() && $webhookData->getStatus() === PaymentStatus::SUCCEEDED;
    }
}