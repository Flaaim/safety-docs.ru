<?php

namespace App\Product\Test\Command\Images\Add;

use App\Flusher;
use App\Product\Command\Images\Add\Command;
use App\Product\Command\Images\Add\Handler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileNameGeneratorInterface;
use App\Product\Service\File\FileUploaderInterface;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use function PHPUnit\Framework\assertCount;

class TestHandler extends TestCase
{
    private ProductRepository $products;
    private FileUploaderInterface $uploader;
    private Handler $handler;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploader = $this->createMock(FileUploaderInterface::class);
        $this->fileNameGenerator = $this->createMock(FileNameGeneratorInterface::class);

        $this->handler = new Handler(
            $this->products,
            $this->flusher,
            $this->uploader,
            $this->fileNameGenerator
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

        $this->uploader->expects(self::never())->method('upload');
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
        $expectedName1 = 'generated_name_1.jpg';
        $expectedName2 = 'generated_name_2.jpg';

        $image1->method('getError')->willReturn(UPLOAD_ERR_OK);
        $image2->method('getError')->willReturn(UPLOAD_ERR_OK);

        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')->with($productId)->willReturn($product);

        $this->uploader->expects($this->exactly(2))->method('upload')
        ->willReturnCallback(
            function ($path, $file) use ($image1, $image2, $expectedName1, $expectedName2){
                if ($file === $image1) {
                    return $expectedName1;
                }
                if ($file === $image2) {
                    return $expectedName2;
                }
                return 'default_name.jpg';
            }
        );

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        $images = $product->getImages();

        assertCount(2, $images);
        self::assertEquals($expectedName1, $images[0]);
        self::assertEquals($expectedName2, $images[1]);
    }
}