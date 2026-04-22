<?php

namespace App\Product\Test\Command\Update;

use App\Flusher;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\Handler;
use App\Product\Entity\Filename;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Service\File\FileUploaderInterface;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private Handler $handler;

    private FileRemoverInterface $fileRemover;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploader = $this->createMock(FileUploaderInterface::class);
        $this->fileRemover = $this->createMock(FileRemoverInterface::class);

        $this->handler = new Handler($this->products, $this->flusher, $this->uploader, $this->fileRemover);
    }

    public function testSuccess(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->once())->method('getClientFilename')->willReturn('new01.1.rar');

        $oldUploadedFile = $this->createMock(UploadedFileInterface::class);
        $oldUploadedFile->expects($this->once())->method('getClientFilename')->willReturn('old01.1.rar');
        $oldFilename = new Filename($oldUploadedFile->getClientFilename());

        $slug = new Slug('education');

        $command = $this->createCommand($slug, $uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withFilename($oldFilename)->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->uploader->expects(self::once())->method('upload')
            ->with($this->equalTo($productId->getValue()), $this->equalTo($uploadedFile));

        $this->fileRemover->expects(self::once())->method('remove')
            ->with($this->equalTo($productId->getValue() . DIRECTORY_SEPARATOR . $oldFilename->getValue()));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
    public function testSuccessWithoutFile(): void
    {
        $filename = new Filename('serv100.1.rar');
        $slug = new Slug('education');
        $command = $this->createCommand($slug);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->uploader->expects(self::never())->method('upload');
        $this->fileRemover->expects(self::never())->method('remove');
        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
    public function testExistsAnotherProductWithSlug(): void
    {
        $filename = new Filename('serv100.1.rar');
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $slug = new Slug('education');
        $command = $this->createCommand($slug, $uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();


        $existingProduct = (new ProductBuilder())->withSlug($slug)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($existingProduct);

        $this->flusher->expects(self::never())->method('flush');
        $uploadedFile->expects($this->never())->method('getClientFilename');
        $this->uploader->expects(self::never())->method('upload');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product with this slug already exists.');

        $this->handler->handle($command);
    }

    public function testProductTheSame(): void
    {
        $filename = new Filename('serv100.1.rar');
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->once())->method('getClientFilename')->willReturn($filename->getValue());
        $slug = new Slug('education');

        $command = $this->createCommand($slug, $uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');

        $product = (new ProductBuilder())
            ->withSlug($slug)
            ->withFilename($filename)
            ->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($product);

        $this->uploader->expects(self::once())->method('upload')
            ->with($this->equalTo('876675c9-6dfb-4db5-bc90-72b73b75616d'), $this->equalTo($uploadedFile));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
        self::assertEquals('edu300.1', $product->getCipher());
        self::assertEquals(550.00, $product->getAmount()->getValue());
        self::assertEquals('serv100.1.rar', $product->getFilename()->getValue());
        self::assertEquals('education', $product->getSlug()->getValue());
    }

    private function createCommand(Slug $slug, ?UploadedFileInterface $uploadedFile = null): Command
    {
        return new Command(
            new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d'),
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            $slug->getValue(),
            (new \DateTimeImmutable())->format('d.m.Y'),
            22,
            ['pdf', 'docx'],
            $uploadedFile
        );
    }
}