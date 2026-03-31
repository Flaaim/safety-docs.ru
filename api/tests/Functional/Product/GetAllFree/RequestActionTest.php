<?php

namespace Test\Functional\Product\GetAllFree;


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
        $response = $this->app()->handle(self::json('GET', '/v1/products/free'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertCount(1, $data);
        self::assertEquals([
            'id' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f',
            'name' => 'Служба охраны труда'
        ], $data[0]);
    }
}