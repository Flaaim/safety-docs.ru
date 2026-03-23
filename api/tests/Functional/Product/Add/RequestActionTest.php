<?php

namespace Functional\Product\Add;

use Test\Functional\Json;
use Test\Functional\WebTestCase;
use function PHPUnit\Framework\assertJson;

class RequestActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/products', $this->getProductData()));

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testExist(): void
    {
        $this->app()->handle(self::json('POST', '/v1/products', $this->getProductData()));
        $response = $this->app()->handle(self::json('POST', '/v1/products', $this->getProductData()));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product with slug electrical already exists.'
        ], $data);
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/products'));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'name' =>  'This value is too short. It should have 5 characters or more.',
                'cipher' =>  'This value should not be blank.',
                'amount' =>  'This value should be positive.',
                'path' =>  'This value should not be blank.',
                'slug' =>  'This value should not be blank.',
                'updatedAt' =>  'This value should not be blank.',
            ]
        ], $data);
    }

    private function getProductData(): array
    {
        return [
            'name' => 'Электробезопасность 1 группа',
            'cipher' => 'serv100.1',
            'amount' => 500.00,
            'path' => 'safety/electrical/electr100.1.rar',
            'slug' => 'electrical',
            'updatedAt' => '01.01.2019'
        ];
    }
}