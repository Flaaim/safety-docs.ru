<?php

namespace App\Shared\Domain\Service\Payment\Provider;

use App\Shared\Domain\Service\Payment\DTO\MakePaymentDTO;
use App\Shared\Domain\Service\Payment\DTO\PaymentCallbackDTO;
use App\Shared\Domain\Service\Payment\DTO\PaymentInfoDTO;
use App\Shared\Domain\Service\Payment\PaymentException;
use App\Shared\Domain\Service\Payment\PaymentProviderInterface;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationFactory;
use YooKassa\Request\Payments\PaymentResponse;

class YookassaProvider implements PaymentProviderInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly YookassaConfig $config
    )
    {}
    public function initiatePayment(MakePaymentDTO $paymentData): PaymentInfoDTO
    {
        $idempotenceKey = uniqid('', true);
        $returnUrl = $this->config->getReturnUrl() . '?token=' . $paymentData->returnToken;
        try{
            $response = $this->client->createPayment([
                'amount' => [
                    'value' => $paymentData->amount,
                    'currency' => $paymentData->currency,
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'locale' => 'ru_RU',
                    'return_url' => $returnUrl,
                ],
                'capture' => true,
                'description' => $paymentData->description,
                'metadata' => $paymentData->metadata,
                'receipt' => [
                    'customer' => [
                        'email' => $paymentData->customerEmail,
                    ]
                ]
            ], $idempotenceKey);

            return new PaymentInfoDTO(
                $response->getId(),
                $response->getStatus(),
                $response->getConfirmation()->getConfirmationUrl()
            );
        }catch (\Exception $e){
            throw new PaymentException($e->getMessage());
        }
    }

    public function handleCallback(PaymentCallbackDTO $callbackData): ?string
    {
        $factory = new NotificationFactory();
        $notificationObject = $factory->factory($callbackData->rawData);
        $responseObject = $notificationObject->getObject();
        /** @var PaymentResponse $responseObject */
        $paymentId = $responseObject->getId();

        return $this->verifyPaymentStatus($paymentId);
    }

    public function checkPaymentStatus(string $paymentId): string
    {
        // TODO: Implement checkPaymentStatus() method.
    }

    public function getSupportedCurrencies(): array
    {
        // TODO: Implement getSupportedCurrencies() method.
    }

    public function getName(): string
    {
        return $this->config->getName();
    }
    private function verifyPaymentStatus(string $paymentId): ?string
    {
        try{
            $payment = $this->client->getPaymentInfo($paymentId);

            return match ($payment->getStatus()) {
                'succeeded', 'waiting_for_capture' => $paymentId,
                'pending', 'canceled' => null,
                default => throw new PaymentException("Unknown payment status: " . $payment->getStatus()),
            };
        }catch (PaymentException $e){
            throw new PaymentException('Failed to verify payment status: ' . $e->getMessage());
        }
    }
}
