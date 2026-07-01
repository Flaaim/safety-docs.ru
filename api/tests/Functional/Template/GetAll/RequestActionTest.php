<?php

namespace Test\Functional\Template\GetAll;

use App\Template\Entity\Slug;
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
        $response = $this->app()->handle(self::json('GET', '/v1/directions'));

        $this->assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            [
                'id' => RequestFixture::DIRECTION_ID,
                'title' => RequestFixture::DIRECTION_TITLE,
                'description' => RequestFixture::DIRECTION_DESCRIPTION,
                'text' => RequestFixture::DIRECTION_TEXT,
                'slug' => Slug::generate(RequestFixture::DIRECTION_TITLE)->getValue()
            ]
        ], $data);
    }
}