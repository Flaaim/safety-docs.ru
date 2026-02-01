<?php

namespace Test\Functional\Payment\CreatePayment;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Test\Functional\Json;
use Test\Functional\WebTestCase;
use Test\Functional\YookassaClient;


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
        $response = $this->app()->handle(self::json('POST', '/payment-service/process-payment', [
            'email' => 'test@app.ru',
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f'
        ]));

        $this->assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);
        self::assertArraySubset([
            'amount' => 350,
            'currency' => 'RUB',
        ],$data);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/process-payment'));

        $this->assertEquals(422, $response->getStatusCode());

        $body = (string)$response->getBody();
        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'email' => 'This value should not be blank.',
                'productId' => 'This value should not be blank.',
            ]
        ], $data);
    }
    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/process-payment', [
            'email' => 'test@app.ru',
            'productId' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ]));

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset([
            'message' => 'Product not found.',
        ], $data);

    }

    public function testInvalidEmail(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/process-payment', [
            'email' => 'invalid',
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8809f'
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.'
            ],
        ], $data);
    }

    public function testInvalidProductId(): void
    {
        $response = $this->app()->handle(self::json('POST', '/payment-service/process-payment', [
            'email' => 'test@user.ru',
            'productId' => 'someInvalidProductId',
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'productId' => 'This is not a valid UUID.',
            ]
        ], $data);
    }

}