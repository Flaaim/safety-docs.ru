<?php

namespace Test\Functional\Template\GetBySlug;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\CategoryBuilder;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{
    public const DIRECTION_ID = 'a597bffd-cdbe-4ac2-b565-639e96957977';
    public function load(ObjectManager $manager): void
    {
        $direction = new Direction(
            new DirectionId(self::DIRECTION_ID),
            $title = 'Охрана труда',
            'Собраны комплекты документов',
            'some text',
            Slug::generate($title)->getValue()
        );
        $manager->persist($direction);


        $directionWithCategories = (new DirectionBuilder())
            ->withId(new DirectionId('5e342356-a499-4349-a2b5-f68d9f7d6d99'))
            ->withTitle('Промышленная безопасность')
            ->withDescription('some description')
            ->withText('some text')
            ->build();

        $category = (new CategoryBuilder())
            ->withCategoryId(new CategoryId('f7c2dc7e-2a88-4c27-9877-3c34d4402d42'))
            ->withTitle('Производственные инструкции')
            ->build($directionWithCategories);

        $manager->persist($category);
        $manager->persist($directionWithCategories);

        $manager->flush();
    }
}