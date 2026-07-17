<?php

declare(strict_types=1);

namespace Test\Functional\Template\Document\GetById;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Document\DocumentId;
use App\Template\Test\Builder\CategoryBuilder;
use App\Template\Test\Builder\DirectionBuilder;
use App\Template\Test\Builder\DocumentBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const DIRECTION_ID = '379f5e5c-d0fc-4dd8-b6af-d031503f6b47';

    public const CATEGORY_ID = '49c16462-90b5-4318-a5f7-4fe8414eec9f';
    public const DOCUMENT_ID = '11927a64-247d-4720-902c-6159e8b65da7';
    public function load(ObjectManager $manager): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId(self::DIRECTION_ID))
            ->build();
        $manager->persist($direction);

        $category = (new CategoryBuilder())
            ->withCategoryId(new CategoryId(self::CATEGORY_ID))
            ->build($direction);
        $manager->persist($category);

        $document = (new DocumentBuilder())
            ->withId(new DocumentId(self::DOCUMENT_ID))
            ->build($category);
        $manager->persist($document);

        $manager->flush();
    }
}
