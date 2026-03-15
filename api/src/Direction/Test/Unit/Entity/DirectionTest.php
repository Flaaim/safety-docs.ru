<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testSuccess(): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('2a7a593a-ee23-4a73-bb07-b372438fb269'))
            ->withTitle('title')
            ->withDescription('description')
            ->withSlug(new Slug('slug'))
            ->withText('text')
            ->build();

        self::assertEquals('title', $direction->getTitle());
        self::assertEquals('description', $direction->getDescription());
        self::assertEquals('slug', $direction->getSlug()->getValue());
        self::assertEquals('text', $direction->getText());
        self::assertEquals('2a7a593a-ee23-4a73-bb07-b372438fb269', $direction->getId()->getValue());
    }


    public function testUpdate(): void
    {

        $direction = (new DirectionBuilder())->build();
        $direction->update('title1', 'description1', 'text1', new Slug('slug'));

        self::assertEquals('title1', $direction->getTitle());
        self::assertEquals('description1', $direction->getDescription());
        self::assertEquals('text1', $direction->getText());
        self::assertEquals('slug', $direction->getSlug()->getValue());


    }

}