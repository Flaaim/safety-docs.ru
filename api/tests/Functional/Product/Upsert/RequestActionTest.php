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
        $response = $this->app()->handle(self::json('POST','/payment-service/products/upsert', [
            'name' => 'ПИ 1792.9 Итоговое тестирование по Программе IIП',
            'cipher' => 'ПИ 1792.9',
            'amount' => 500.00,
            'path' => 'fire/1792/pi1792.9.docx',
            'course' => '1792'
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);
        self::assertArrayHasKey('productId', $data);
    }

    public function testUpdateProduct(): void
    {
        $response = $this->app()->handle(self::json('POST','/payment-service/products/upsert', [
            'name' => 'ПИ 1791.11 Итоговое тестирование по Программе IП',
            'cipher' => 'ПИ 1791.11',
            'amount' => 500.00,
            'path' => 'fire/1791/pi1791.11.docx',
            'course' => '1791'
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);
        self::assertArrayHasKey('productId', $data);
        self::assertEquals('b38e76c0-ac23-4c48-85fd-975f32c8801f', $data['productId']);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST','/payment-service/products/upsert', []));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'name' => 'This value is too short. It should have 5 characters or more.',
                'cipher' => 'This value should not be blank.',
                'amount' => 'This value should be positive.',
                'path' => 'This value should not be blank.',
                'course' => 'This value should not be blank.',
            ]
        ], $data);

    }
}