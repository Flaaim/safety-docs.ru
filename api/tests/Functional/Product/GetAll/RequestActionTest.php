<?php

namespace Test\Functional\Product\GetAll;

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
        $response = $this->app()->handle(self::json('GET', '/v1/products'));

        $this->assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'products' => [
                [
                    'name' => 'Служба охраны труда',
                    'price' => '550.00 RUB',
                    'cipher' => 'serv100.1',
                    'file' => 'safety/service/serv100.1.rar',
                    'slug' => 'service',
                    'id' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f'
                ]
            ],
            'total' => 1,
            'currentPage' => 1,
            'perPage' => 20,
            'totalPages' => 1,
        ], $data);

    }

}