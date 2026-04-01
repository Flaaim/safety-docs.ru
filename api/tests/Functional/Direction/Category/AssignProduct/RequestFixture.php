<?php

namespace Test\Functional\Direction\Category\AssignProduct;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Product\Entity\ProductId;
use App\Product\Test\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $category = new Category(
            new CategoryId('15823c37-3358-44be-96dc-363d56bde91c'),
            'Служба охраны труда',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Some simple text',
            new Slug('service'),
            $direction = (new DirectionBuilder())->withId(new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'))->build()
        );


        $product = (new ProductBuilder())->withId(new ProductId('8192666c-03bb-4487-8d9f-08ea7cd4afec'))->build();
        $manager->persist($direction);

        $manager->persist($category);

        $manager->persist($product);

        $manager->flush();
    }
}