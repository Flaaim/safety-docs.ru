<?php

namespace Test\Functional\Payment\Result;


use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Test\Functional\Json;
use Test\Functional\Payment\PaymentBuilder;
use Test\Functional\Payment\TokenBuilder;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    use ArraySubsetAsserts;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }
    public function testSuccess(): void
    {
        $returnToken = (new TokenBuilder())->build();

        $response = $this->app()->handle(self::json('POST', '/payment-service/result', [
            'returnToken' => $returnToken->getValue()
        ]));

        $this->assertEquals(200, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset([
            'returnToken' => $returnToken->getValue(),
            'status' => 'succeeded',
            'email' => 'test@app.ru'
        ], $data);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/result'));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'returnToken' => 'This value should not be blank.',
            ]
        ], $data);

    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/result', [
            'returnToken' => 'invalid'
        ]));
        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'returnToken' => 'Token is not correct.',
            ]
        ], $data);
    }
    public function testExpired(): void
    {
        $expiredPayment = (new PaymentBuilder())
            ->withExpiredToken()
            ->build();

        $response = $this->app()->handle(self::json('POST', '/payment-service/result', [
            'returnToken' => $expiredPayment->getReturnToken()->getValue()
        ]));

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertArraySubset([
           'message' => 'Token is expired.',
        ], $data);
    }

}