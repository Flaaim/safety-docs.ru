<?php

declare(strict_types=1);

namespace Test\Functional\Template\Document\MultipleUpload;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const DIRECTION_ID = '37e9c865-8401-4339-bb23-73a25b85e7b3';
    public const PARENT_CATEGORY_ID  = '1fd015e7-1b9e-4c1d-8aa3-7148bfc6e522';
    public const CHILD_CATEGORY_ID = '14030181-6192-4051-8ad1-18edb32f6723';
    public const CATEGORY_NOT_FOUND = '69061836-32ac-433e-abfc-8710de5e162b';
    public function load(ObjectManager $manager): void
    {
        $parentCategory = new Category(
            new CategoryId(self::PARENT_CATEGORY_ID),
            $title = 'Инструкции',
            'Собраны комплекты инструкций по охране труда',
            'Some simple text',
            Slug::generate($title)->getValue(),
            $direction = (new DirectionBuilder())->withId(new DirectionId(self::DIRECTION_ID))->build(),
        );
        $manager->persist($direction);
        $manager->persist($parentCategory);

        $childCategory = new Category(
            new CategoryId(self::CHILD_CATEGORY_ID),
            $title = 'Инструкция при работах на высоте',
            'Some simple text',
            'Text',
            Slug::generate($title)->getValue(),
            $direction,
            $parentCategory
        );
        $manager->persist($childCategory);

        $manager->flush();
    }
}