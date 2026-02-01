<?php

namespace App\Payment\Command\HookPayment;

use App\Flusher;
use App\Payment\Command\HookPayment\SendProduct\Command as SendProductCommand;
use App\Payment\Command\HookPayment\SendProduct\Handler as SendProductHandler;
use App\Payment\Entity\PaymentRepository;
use App\Shared\Domain\Service\Payment\DTO\PaymentCallbackDTO;
use App\Shared\Domain\Service\Payment\PaymentProviderInterface;
use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;
use Psr\Log\LoggerInterface;

class Handler
{
    public function __construct(
        private readonly PaymentWebhookParserInterface  $webhookParser,
        private readonly PaymentProviderInterface       $provider,
        private readonly PaymentRepository $paymentRepository,
        private readonly Flusher $flusher,
        private readonly SendProductHandler  $sendProductHandler,
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
        $payment = $this->paymentRepository->getByExternalId($paymentId);
        $paymentWebHookData = $this->webhookParser->parse($callbackDTO->rawData);

        try{
            $this->sendProductHandler->handle(new SendProductCommand($payment, $paymentWebHookData));

            $this->paymentRepository->update($payment);

            $this->flusher->flush();
        }catch (\Exception $e){
            $this->logger->error('Failed to handle webhook', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to send product: ' . $e->getMessage());
        }




    }

}