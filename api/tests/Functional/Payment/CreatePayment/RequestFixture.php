<?php

namespace Test\Functional\Payment\CreatePayment;

use App\Product\Test\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = (new ProductBuilder())->build();

        $manager->persist($product);

        $manager->flush();
    }
}