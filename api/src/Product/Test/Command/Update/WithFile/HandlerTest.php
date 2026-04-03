<?php

namespace App\Product\Test\Command\Update\WithFile;

use App\Flusher;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\WithFile\Handler;
use App\Product\Command\Add\Upload\Handler as UploadHandler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private Handler $handler;
    private UploadHandler $uploadHandler;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);
        $this->handler = new Handler($this->products, $this->flusher, $this->uploadHandler);
    }

    public function testSuccess(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->once())->method('getClientFilename')->willReturn('test.rar');
        $slug = new Slug('education');
        $command = $this->createCommand($slug, $uploadedFile);


        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->uploadHandler->expects(self::once())->method('handle')
            ->with($this->equalTo('201/test.rar'), $this->equalTo($uploadedFile));


        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
    public function testExistsAnotherProductWithSlug(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $slug = new Slug('education');
        $command = $this->createCommand($slug, $uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();


        $existingProduct = (new ProductBuilder())->withSlug($slug)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($existingProduct);

        $this->flusher->expects(self::never())->method('flush');
        $uploadedFile->expects($this->never())->method('getClientFilename');
        $this->uploadHandler->expects(self::never())->method('handle');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product with this slug already exists.');

        $this->handler->handle($command);
    }

    public function testProductTheSame(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->once())->method('getClientFilename')->willReturn('test.rar');
        $slug = new Slug('education');

        $command = $this->createCommand($slug, $uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');

        $product = (new ProductBuilder())->withSlug($slug)->withId($productId)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($product);

        $this->uploadHandler->expects(self::once())->method('handle')
            ->with($this->equalTo('201/test.rar'), $this->equalTo($uploadedFile));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
        self::assertEquals('edu300.1', $product->getCipher());
        self::assertEquals(550.00, $product->getAmount()->getValue());
        self::assertEquals('education', $product->getSlug()->getValue());
    }

    private function createCommand(Slug $slug, UploadedFileInterface $uploadedFile): Command
    {
        return new Command(
            new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d'),
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            $slug->getValue(),
            (new \DateTimeImmutable())->format('d.m.Y'),
            $uploadedFile
        );
    }
}