<?php

namespace App\Product\Test\Command\Images\Clear;

use App\Flusher;
use App\Product\Command\Images\Clear\Command;
use App\Product\Command\Images\Clear\Handler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private FileRemoverInterface $fileRemover;
    private Handler $handler;
    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->fileRemover = $this->createMock(FileRemoverInterface::class);

        $this->handler = new Handler($this->products, $this->flusher, $this->fileRemover);
    }
    public function testNotFound(): void
    {
        $productId = new ProductId('89309b56-4c7b-4cda-886d-7f39765cd620');
        $command = new Command($productId);

        $this->products->expects(self::once())->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product not found.');
        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $productId = new ProductId('89309b56-4c7b-4cda-886d-7f39765cd620');
        $command = new Command($productId);
        $product = (new ProductBuilder())
            ->withId($productId)
            ->withImages(['image1', 'image2'])
            ->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->flusher->expects(self::once())->method('flush');

        $this->fileRemover->expects($matcher = self::exactly(2))
            ->method('remove')
            ->willReturnCallback(function (string $filePath) use ($matcher, $productId) {
                $expected = match($matcher->numberOfInvocations()){
                    1 => '89309b56-4c7b-4cda-886d-7f39765cd620'. DIRECTORY_SEPARATOR .'image1',
                    2 => '89309b56-4c7b-4cda-886d-7f39765cd620'.DIRECTORY_SEPARATOR.'image2'
                };
                $this->assertSame($expected, $filePath);
            });

        $this->handler->handle($command);

        self::assertEmpty($product->getImages());
    }
}