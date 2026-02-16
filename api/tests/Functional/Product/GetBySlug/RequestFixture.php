<?php

namespace Functional\Product\GetBySlug;

use App\Product\Entity\Currency;
use App\Product\Entity\File;
use App\Product\Entity\Price;
use App\Product\Entity\ProductId;
use App\Product\Test\ProductBuilder;
use App\Shared\Domain\ValueObject\Id;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = (new ProductBuilder())
            ->withId(new ProductId('b38e76c0-ac23-4c48-85fd-975f32c8801f'))
            ->withName('Служба охраны труда')
            ->withCipher('serv100.1')
            ->withPrice(new Price(550.00, new Currency('RUB')))
            ->withFile(new File('safety/service/serv100.1.rar'))
            ->withSlug('service')
            ->build();

        $manager->persist($product);

        $manager->flush();
    }
}