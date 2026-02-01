<?php

namespace App\Payment\Service\Delivery;

use App\Payment\Entity\Email;
use App\Payment\Service\ProductSender;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;
use App\Shared\Domain\ValueObject\Id;

class ProductDeliveryService implements ProductDeliveryInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductSender $sender
    )
    {}
    public function deliver(PaymentWebhookDataInterface $paymentWebHookData): void
    {
        $productId = $paymentWebHookData->getMetadata('productId');
        $email = $paymentWebHookData->getMetadata('email');

        if(!$productId || !$email){
            throw new \DomainException('Missing required metadata in webhook');
        }
        /** @var ProductSender $sender */
        $this->sender->send(
            new Email($email),
            $this->productRepository->get(new Id($productId))
        );
    }
}