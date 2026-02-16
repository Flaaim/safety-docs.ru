<?php

namespace Test\Functional\Product\Get;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/payment-service/products/get?id=b38e76c0-ac23-4c48-85fd-975f32c8801f'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'name' => 'Служба охраны труда',
            'formattedPrice' => '550.00 RUB',
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f'
        ], $data);
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/payment-service/products/get?id=b38e76c0-ac23-4c48-85fd-975f32c8800f'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);

    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('GET', '/payment-service/products/get?id=not-uuid-string'));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'productId' => 'This is not a valid UUID.'
            ]
        ], $data);
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('GET', '/payment-service/products/get'));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'productId' => 'This value should not be blank.'
        ]], $data);
    }
}