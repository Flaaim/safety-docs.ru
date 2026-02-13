<?php

namespace App\Payment\Command\HookPayment;

use App\Flusher;
use App\Payment\Entity\DTO\PaymentCallbackDTO;
use App\Payment\Entity\PaymentRepository;
use App\Payment\Entity\Status;
use App\Shared\Domain\Event\Payment\SuccessfulPaymentEvent;
use App\Shared\Domain\Service\Payment\PaymentProviderInterface;
use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Handler
{
    public function __construct(
        private readonly PaymentWebhookParserInterface  $webhookParser,
        private readonly PaymentProviderInterface       $provider,
        private readonly PaymentRepository              $payments,
        private readonly Flusher $flusher,
        private readonly EventDispatcherInterface  $dispatcher,
        private readonly LoggerInterface $logger,
    )
    {}
    public function handle(Command $command): void
    {
        $callbackDTO = new PaymentCallbackDTO(
            $command->data,
            $_SERVER['HTTP_CONTENT_SIGNATURE'] ?? '',
            $this->provider->getName()
        );

        if(!$this->webhookParser->supports($callbackDTO->provider, $callbackDTO->rawData)){
            throw new \RuntimeException('Unsupported webhook format');
        }

        $paymentId = $this->provider->handleCallback($callbackDTO);

        if (null === $paymentId) {
            return;
        }

        $payment = $this->payments->getByExternalId($paymentId);

        if ($payment->getStatus()->isSucceeded()){
            $this->logger->info('Payment already processed ', ['paymentId' => $paymentId]);
            return;
        }

        try{
            $payment->updateStatus(Status::succeeded());

            $this->payments->update($payment);

            $this->flusher->flush();

            $event = new SuccessfulPaymentEvent($payment);
            $this->dispatcher->dispatch($event);
        }catch (\Exception $e){
            $this->logger->error('Failed to handle webhook', [
                'error' => $e->getMessage(),
                'paymentId' => $paymentId
            ]);
            throw new \RuntimeException('Failed to process payment: ' . $e->getMessage());
        }

    }

}