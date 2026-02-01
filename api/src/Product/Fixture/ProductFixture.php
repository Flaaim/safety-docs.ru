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
            'СИЗ образцы документов',
            new Price(450.00, new Currency()),
            new File('/ppe/templates.txt'),
            'ot161.4',
            '161'
        );

        $manager->persist($product);

        $manager->flush();
    }
}