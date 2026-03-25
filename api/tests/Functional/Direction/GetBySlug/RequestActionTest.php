<?php

namespace Test\Functional\Direction\GetBySlug;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class
        ]);
    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/s/safety'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'id' => 'a597bffd-cdbe-4ac2-b565-639e96957977',
            'slug' => 'safety',
            'title' => $data['title'],
            'description' => $data['description'],
            'text' => $data['text'],
            'categories' => []
        ], $data);

    }

    public function testNotExist(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/s/not-exist'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Direction not found.'], $data);
    }

}