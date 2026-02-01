<?php

namespace Test\Functional\Payment\CreatePayment;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Test\Functional\Payment\ProductBuilder;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = (new ProductBuilder())->build();

        $manager->persist($product);

        $manager->flush();
    }
}