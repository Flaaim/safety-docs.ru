<?php

namespace App\Product\Test\Command\Images\Add;


use App\Flusher;
use App\Product\Command\Images\Add\Command;
use App\Product\Command\Images\Add\Handler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Command\Images\Add\Upload\Handler as UploadHandler;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class TestHandler extends TestCase
{
    private ProductRepository $products;
    private Handler $handler;

    private UploadHandler $uploadHandler;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);

        $this->handler = new Handler(
            $this->products,
            $this->flusher,
            $this->uploadHandler
        );
    }


    public function testNotFound(): void
    {
        $productId = new ProductId('213df811-7190-4e69-913b-f0c8483d1416');
        $command = new Command($productId->getValue(), [
            $image1 = $this->createMock(UploadedFileInterface::class),
            $image2 = $this->createMock(UploadedFileInterface::class),
        ]);

        self::expectException(\DomainException::class);

        $this->products->expects(self::once())->method('findById')->with($productId)->willReturn(null);

        $this->uploadHandler->expects(self::never())->method('handle');
        $this->flusher->expects(self::never())->method('flush');

        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $productId = new ProductId('213df811-7190-4e69-913b-f0c8483d1416');
        $command = new Command($productId->getValue(), [
            $image1 = $this->createMock(UploadedFileInterface::class),
            $image2 = $this->createMock(UploadedFileInterface::class),
        ]);
        $image1->expects($this->once())->method('getClientFilename')->willReturn('image1');
        $image2->expects($this->once())->method('getClientFilename')->willReturn('image2');

        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')->with($productId)->willReturn($product);

        $this->uploadHandler->expects(self::once())->method('handle')
            ->with($this->equalTo($command));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
}