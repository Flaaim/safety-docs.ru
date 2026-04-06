<?php

namespace App\Product\Test\Command\Add;

use App\Flusher;
use App\Product\Command\Add\Command;
use App\Product\Command\Add\Handler;
use App\Product\Command\Upload\Handler as UploadHandler;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private UploadHandler $uploadHandler;
    private Handler $handler;
    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);
        $this->handler = new Handler($this->products, $this->flusher, $this->uploadHandler);
    }

    public function testExists(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $command = $this->createCommand($uploadedFile);

        $slug = new Slug($command->slug);
        $product = (new ProductBuilder())->build();
        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($product);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product with slug '.$command->slug.' already exists');

        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $command = $this->createCommand($uploadedFile);

        $slug = new Slug($command->slug);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->products->expects(self::once())->method('add')
            ->with(self::callback(function(Product $product) {
                self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
                self::assertEquals('edu300.1', $product->getCipher());
                self::assertEquals(550.00, $product->getAmount()->getValue());
                self::assertEquals('safety/education', $product->getFile()->getValue());
                self::assertEquals('education', $product->getSlug()->getValue());
                self::assertEquals(22, $product->getTotalDocuments());
                self::assertEquals([FormatDocument::DOCX, FormatDocument::PDF], $product->getFormatDocuments());
                return true;
            }));

        $this->uploadHandler->expects(self::once())->method('handle')
            ->with(
                $this->equalTo('safety/education'),
                $this->equalTo($uploadedFile)
            );

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }

    private function createCommand(UploadedFileInterface $uploadedFile): Command
    {
        return new Command(
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            'safety/education',
            'education',
            (new \DateTimeImmutable())->format('d.m.Y'),
            $uploadedFile,
            22,
            ['docx', 'pdf'],
        );
    }
}