<?php

namespace App\Product\Test\Command\Update;

use App\Flusher;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\Handler;
use App\Product\Entity\Filename;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Service\File\FileUploaderInterface;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    /** @var ProductRepository&MockObject  */
    private ProductRepository $products;
    /** @var Flusher&MockObject  */
    private Flusher $flusher;
    private Handler $handler;
    /** @var FileRemoverInterface&MockObject  */
    private FileRemoverInterface $fileRemover;
    /** @var FileUploaderInterface&MockObject  */
    private FileUploaderInterface $uploader;

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

        $command = $this->createCommand($uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withFilename($oldFilename)->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->uploader->expects(self::once())->method('upload')
            ->with($this->equalTo($productId->getValue()), $this->equalTo($uploadedFile));

        $this->fileRemover->expects(self::once())->method('remove')
            ->with($this->equalTo($productId->getValue() . DIRECTORY_SEPARATOR . $oldFilename->getValue()));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
    public function testSuccessWithoutFile(): void
    {
        $command = $this->createCommand();
        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->uploader->expects(self::never())->method('upload');
        $this->fileRemover->expects(self::never())->method('remove');
        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }

    public function testProductTheSame(): void
    {
        $filename = new Filename('serv100.1.rar');
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->once())->method('getClientFilename')->willReturn($filename->getValue());

        $command = $this->createCommand($uploadedFile);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');

        $product = (new ProductBuilder())
            ->withFilename($filename)
            ->withId($productId)->build();

        $this->products->expects(self::once())->method('findById')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->uploader->expects(self::once())->method('upload')
            ->with($this->equalTo('876675c9-6dfb-4db5-bc90-72b73b75616d'), $this->equalTo($uploadedFile));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
        self::assertEquals('edu300.1', $product->getCipher());
        self::assertEquals(550.00, $product->getAmount()->getValue());
        self::assertEquals('serv100.1.rar', $product->getFilename()->getValue());
    }

    private function createCommand(?UploadedFileInterface $uploadedFile = null): Command
    {
        return new Command(
            new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d'),
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            (new \DateTimeImmutable())->format('d.m.Y'),
            22,
            ['pdf', 'docx'],
            $uploadedFile
        );
    }
}