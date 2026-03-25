<?php

namespace Test\Functional\Direction\Delete;

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
        $response = $this->app()->handle(self::json('DELETE', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948'));

        self::assertEquals(204, $response->getStatusCode());
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0455'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);
        self::assertEquals([
            'message' => 'Direction not found.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/directions/invalid'));

        self::assertEquals(404, $response->getStatusCode());
    }
}