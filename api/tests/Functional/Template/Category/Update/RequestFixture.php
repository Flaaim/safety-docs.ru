<?php

namespace Test\Functional\Template\Category\Update;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('e42b8e4f-0ac3-4cca-984d-4f1dc983e970'))
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Охрана труда текст')
            ->build();

        $newDirection = (new DirectionBuilder())
            ->withId(new DirectionId('8b4929fa-51cc-4df9-90b6-071848e5b977'))
            ->withTitle('Пожарная безопасность')
            ->withDescription('Пожарная безопасность описание')
            ->withText('Пожарная безопасность текст')
            ->build();

        $category = new Category(
            new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'),
            $title = 'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $anotherCategory = new Category(
            new CategoryId('8ccca2f8-5a57-47a6-82e1-c3d490e8f18b'),
            $title = 'Обучение по охране труда',
            'Описание обучения по охране труда',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $manager->persist($direction);

        $manager->persist($newDirection);

        $manager->persist($category);

        $manager->persist($anotherCategory);

        $manager->flush();
    }


}