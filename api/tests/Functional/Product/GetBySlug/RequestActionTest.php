<?php

namespace Functional\Product\GetBySlug;

use Test\Functional\Json;
use Test\Functional\Product\Get\RequestFixture;
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
        $response = $this->app()->handle(self::json('GET', '/v1/products/get/service'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'name' => 'Служба охраны труда',
            'formattedPrice' => '550.00 RUB',
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f',
            'updatedAt' => (new \DateTimeImmutable())->format('d.m.Y'),
        ], $data);
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/products/get/not-exist'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);
    }

}