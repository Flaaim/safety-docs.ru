<?php

namespace App\Product\Test\Command\Add;

use App\Flusher;
use App\Product\Command\Add\Command;
use App\Product\Command\Add\Handler;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Service\File\FileUploaderInterface;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private Handler $handler;
    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploader = $this->createMock(FileUploaderInterface::class);
        $this->handler = new Handler($this->products, $this->flusher,  $this->uploader);
    }

    public function testSlugExists(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $command = $this->createCommand($uploadedFile);

        $slug = new Slug($command->slug);
        $product = (new ProductBuilder())->build();
        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($product);

        $this->flusher->expects(self::never())->method('flush');
        $this->products->expects(self::never())->method('add');


        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product with slug '.$command->slug.' already exists');

        $this->handler->handle($command);
    }
    public function testFilenameFileNotEquals(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $command = $this->createCommand($uploadedFile, 'test1.rar');
        $uploadedFile->expects(self::once())->method('getClientFilename')->willReturn('test2.rar');

        $slug = new Slug($command->slug);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Filename and name of file name is not equals.');

        $this->flusher->expects(self::never())->method('flush');
        $this->products->expects(self::never())->method('add');

        $this->handler->handle($command);
    }
    public function testSuccess(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects(self::once())->method('getClientFilename')->willReturn($filename = 'test.rar');
        $command = $this->createCommand($uploadedFile, $filename);

        $slug = new Slug($command->slug);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->products->expects(self::once())->method('add')
            ->with(self::callback(function(Product $product) {
                self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
                self::assertEquals('edu300.1', $product->getCipher());
                self::assertEquals(550.00, $product->getAmount()->getValue());
                self::assertEquals('test.rar', $product->getFilename()->getValue());
                self::assertEquals('education', $product->getSlug()->getValue());
                self::assertEquals(22, $product->getTotalDocuments());
                self::assertEquals([FormatDocument::DOCX, FormatDocument::PDF], $product->getFormatDocuments());
                return true;
            }));

        $this->uploader->expects(self::once())->method('upload');
        $this->flusher->expects(self::once())->method('flush');


        $this->handler->handle($command);
    }

    private function createCommand(UploadedFileInterface $uploadedFile, string $filename = 'test.rar'): Command
    {
        return new Command(
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            $filename,
            'education',
            (new \DateTimeImmutable())->format('d.m.Y'),
            $uploadedFile,
            22,
            ['docx', 'pdf'],
        );
    }
}