<?php

namespace Test\Functional\Direction\Category\GetAllByDirection;

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
        $response = $this->app()->handle(self::json('GET', '/v1/directions/37e9c865-8401-4339-bb23-73a25b85e7b3/categories'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'categories' => [
                [
                    'id' => '15823c37-3358-44be-96dc-363d56bde91c',
                    'title' => 'Служба охраны труда',
                    'description' => 'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
                    'text' => 'Some simple text',
                    'slug' => 'service',
                    'direction_id' => '37e9c865-8401-4339-bb23-73a25b85e7b3'
                ]
            ],
            'total' => 1,
        ], $data);
    }

    public function testNotExist(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/37e9c865-8401-4339-bb23-73a25b85e7b5/categories'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Categories not found.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/invalid_string/categories'));

        self::assertEquals(404, $response->getStatusCode());
    }
}