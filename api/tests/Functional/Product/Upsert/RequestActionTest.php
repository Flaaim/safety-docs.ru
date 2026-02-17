<?php

namespace Test\Functional\Product\Upsert;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }

    public function testAddProduct(): void
    {
        $response = $this->app()->handle(self::json('POST','/v1/products/upsert', [
            'name' => 'Служба охраны труда',
            'cipher' => 'serv100.1',
            'amount' => 500.00,
            'path' => 'safety/service/serv100.1.rar',
            'slug' => 'service'
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);
        self::assertArrayHasKey('productId', $data);
    }

    public function testUpdateProduct(): void
    {
        $response = $this->app()->handle(self::json('POST','/v1/products/upsert', [
            'name' => 'Служба охраны труда',
            'cipher' => 'serv100.1',
            'amount' => 500.00,
            'path' => 'safety/service/serv100.1.rar',
            'slug' => 'service'
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);
        self::assertArrayHasKey('productId', $data);
        self::assertEquals('b38e76c0-ac23-4c48-85fd-975f32c8801f', $data['productId']);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST','/v1/products/upsert', []));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'name' => 'This value is too short. It should have 5 characters or more.',
                'cipher' => 'This value should not be blank.',
                'amount' => 'This value should be positive.',
                'path' => 'This value should not be blank.',
                'slug' => 'This value should not be blank.',
            ]
        ], $data);

    }
}