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
        $slug = 'safety';
        $response = $this->app()->handle(self::json('GET', '/v1/directions/get/'.$slug));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'slug' => $slug,
            'title' => $data['title'],
            'description' => $data['description'],
            'text' => $data['text'],
        ], $data);

    }

    public function testNotExist(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/get/not-exist'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Direction not found.'], $data);
    }

}