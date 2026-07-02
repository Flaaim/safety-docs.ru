<?php

namespace Test\Functional\Payment\CreatePayment;

use App\Template\Test\Builder\DocumentBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $document = (new DocumentBuilder())->build();

        $manager->persist($document);

        $manager->flush();
    }
}