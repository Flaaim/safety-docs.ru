<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testUpdate(): void
    {
        $serviceCategory = $this->getServiceCategory();

        $fireDirection = (new DirectionBuilder())
            ->withId(new DirectionId('d3a46a04-fdeb-4efa-b7d2-593d38bbf33d'))
            ->withTitle('Пожарная безопасность')
            ->build();

        $serviceCategory->update(
            'Обучение по пожарной безопасности',
            'Обучение по пожарной безопасности, комплект документов',
            'Some text',
            new Slug('education'),
            $fireDirection
        );
        self::assertEquals('Обучение по пожарной безопасности', $serviceCategory->getTitle());
        self::assertEquals('Обучение по пожарной безопасности, комплект документов', $serviceCategory->getDescription());
        self::assertEquals('Some text', $serviceCategory->getText());
        self::assertEquals('education', $serviceCategory->getSlug()->getValue());
        self::assertEquals($fireDirection, $serviceCategory->getDirection());
    }

    private function getServiceCategory(): Category
    {
        $safetyDirection = (new DirectionBuilder())
            ->withId(new DirectionId('a393dded-51c5-4049-91ff-414b37ddf917'))
            ->withTitle('Охран труда')
            ->build();

        return new Category(
            new CategoryId('d5288bf5-6919-41cf-912b-5ec1ae4649d7'),
            'Служба охраны труда',
            'Служба охраны труда, комплект документов',
            'some text',
            new Slug('service'),
            $safetyDirection
        );
    }

}