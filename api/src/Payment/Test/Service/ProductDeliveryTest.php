<?php

namespace App\Payment\Test\Service;

use App\Payment\Entity\Email;
use App\Payment\Service\Delivery\ProductDeliveryService;
use App\Payment\Service\ProductSender;
use App\Product\Entity\Product;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;
use App\Shared\Domain\ValueObject\Id;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductDeliveryTest extends TestCase
{
    public function testSuccess(): void
    {
        $sender = $this->createMock(ProductSender::class);
        $repository = $this->createMock(ProductRepository::class);
        $paymentWebHookData = $this->createMock(PaymentWebhookDataInterface::class);


        $delivery = new ProductDeliveryService($repository, $sender);

        $paymentWebHookData->method('getMetadata')->willReturnCallback(fn(string $key) => match ($key){
            'productId' => 'da13a321-37ca-44c7-9434-42e9ae9a3043',
            'email' => 'test@example.com',
            default => null,
        });

        $repository->expects($this->once())->method('get')->with(
            $this->equalTo(new Id('da13a321-37ca-44c7-9434-42e9ae9a3043')),
        )->willReturn($product = $this->createMock(Product::class));


        $sender->expects($this->once())->method('send')->with(
            $this->equalTo(new Email('test@example.com')),
            $this->equalTo($product)
        );
        $delivery->deliver($paymentWebHookData);
    }

    #[DataProvider('metadataProvider')]
    public function testFailed($data): void
    {
        $sender = $this->createMock(ProductSender::class);
        $repository = $this->createMock(ProductRepository::class);
        $paymentWebHookData = $this->createMock(PaymentWebhookDataInterface::class);

        $delivery = new ProductDeliveryService($repository, $sender);

        $paymentWebHookData->method('getMetadata')->willReturnCallback(
            fn(string $key) => $data[$key] ?? null
        );
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Missing required metadata in webhook');
        $delivery->deliver($paymentWebHookData);
    }

    public static function metadataProvider(): array
    {
        return [
            [
                [
                    'productId' => null,
                    'email' => 'test@example.com'
                ],
            ],
            [
                [
                    'productId' => null,
                    'email' => null
                ],
            ],
            [
                [
                    'productId' => 'da13a321-37ca-44c7-9434-42e9ae9a3043',
                    'email' => null
                ],
            ]
        ];
    }
}