<?php

namespace App\Product\Fixture;

use App\Product\Entity\Currency;
use App\Product\Entity\File;
use App\Product\Entity\Price;
use App\Product\Entity\Product;
use App\Shared\Domain\ValueObject\Id;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = new Product(
            new Id('b38e76c0-ac23-4c48-85fd-975f32c8801f'),
            'Полный комплект ЛНА по охране труда',
            new Price(2550.00, new Currency('RUB')),
            new File('/ppe/templates.txt'),
            'lna2026.1',
            'lna2026'
        );

        $manager->persist($product);

        $manager->flush();
    }
}