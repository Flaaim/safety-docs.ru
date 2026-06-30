<?php

declare(strict_types=1);

namespace Test\Functional\Document\MultipleUpload;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const  PARENT_CATEGORY_ID  = '1fd015e7-1b9e-4c1d-8aa3-7148bfc6e522';

    public const CHILD_CATEGORY_ID = '14030181-6192-4051-8ad1-18edb32f6723';
    public function load(ObjectManager $manager): void
    {
        $parentCategory = new Category(
            new CategoryId(self::PARENT_CATEGORY_ID),
            $title = 'Инструкции',
            'Собраны комплекты инструкций по охране труда',
            'Some simple text',
            Slug::generate($title),
            $direction = (new DirectionBuilder())->withId(new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'))->build(),
        );
        $manager->persist($direction);
        $manager->persist($parentCategory);

        $childCategory = new Category(
            new CategoryId(self::CHILD_CATEGORY_ID),
            $title = 'Инструкция при работах на высоте',
            'Some simple text',
            'Text',
            Slug::generate($title),
            $direction,
            $parentCategory
        );
        $manager->persist($childCategory);

        $manager->flush();
    }
}