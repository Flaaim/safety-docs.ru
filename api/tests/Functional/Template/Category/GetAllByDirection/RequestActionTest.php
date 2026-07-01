<?php

namespace Test\Functional\Template\Category\GetAllByDirection;

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
        $response = $this->app()->handle(self::json('GET', '/v1/directions/'. RequestFixture::DIRECTION_ID .'/categories'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
                [
                    'id' => '15823c37-3358-44be-96dc-363d56bde91c',
                    'title' => 'Служба охраны труда',
                    'description' => 'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
                    'text' => 'Some simple text',
                    'slug' => 'service',
                    'directionId' => RequestFixture::DIRECTION_ID,
                    'parentId' => null,
                    'children' => []
                ]
        ], $data);
    }

    public function testNotExist(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/'. RequestFixture::DIRECTION_NOT_FOUND_ID .'/categories'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions/invalid_string/categories'));

        self::assertEquals(404, $response->getStatusCode());
    }
}