<?php

namespace Test\Functional\Payment\Result;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Test\Functional\Payment\PaymentBuilder;
use Test\Functional\Payment\TokenBuilder;
use Test\Functional\YookassaClient;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $paymentId = (new YookassaClient())->getLastPayment();
        if($paymentId === null) {
            throw new \Exception('Payment id is null');
        }

        $validPayment = (new PaymentBuilder())
            ->withExternalId($paymentId)
            ->withSucceededStatus()
            ->build();

        $manager->persist($validPayment);

        $expiredPayment = (new PaymentBuilder())
            ->withExternalId($paymentId)
            ->withSucceededStatus()
            ->withExpiredToken()
            ->build();



        $manager->persist($expiredPayment);

        $manager->flush();
    }
}