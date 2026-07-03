<?php

declare(strict_types=1);

namespace App\Template\Test\Unit\Entity\Document;

use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Category\Category;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\Filename;
use App\Template\Test\Builder\CategoryBuilder;
use App\Template\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class DocumentTest extends TestCase
{
    private Category $category;
    public function setUp(): void
    {
        $direction = (new DirectionBuilder())->build();
        $this->category = (new CategoryBuilder())
            ->withTitle('Инструкции по охране труда')
            ->build($direction);
    }
    public function testCreate(): void
    {
        $document = new Document(
            $id = DocumentId::generate(),
            $name = 'Инструкция по охране труда при работе на высоте',
            $amount = new Amount(200.00, new Currency('RUB')),
            $filename = new Filename(Uuid::uuid4()->toString(). '.docx'),
            $slug = 'instructions',
            $this->category,
        );

        self::assertEquals($id, $document->getId());
        self::assertEquals($name, $document->getName());
        self::assertEquals($amount->getValue(), $document->getAmount()->getValue());
        self::assertEquals($filename, $document->getFilename()->getValue());
        self::assertEquals($slug, $document->getSlug());

        self::assertEquals($this->category, $document->getCategory());
        self::assertCount(1, $this->category->getDocuments());
    }
}