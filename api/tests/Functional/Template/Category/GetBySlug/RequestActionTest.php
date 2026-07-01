<?php

namespace Test\Functional\Template\Category\GetBySlug;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp():void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/'. RequestFixture::DIRECTION_ID .'/categories/s/instructions')
        );

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'id' => RequestFixture::CATEGORY_ID,
            'title' => 'Инструкции по охране труда',
            'description' => 'Различные инструкции по охране труда',
            'text' => 'Some text',
            'slug' => 'instructions',
            'directionId' => RequestFixture::DIRECTION_ID,
            'parentId' => null,
            'children' => []
        ], $data);
    }
    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/s/sout'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/s/sout12!!'
        ));

        self::assertEquals(404, $response->getStatusCode());
    }

    public function testNotFoundDirection(): void
    {
        $response = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e971/categories/s/service'
        ));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.',
        ], $data);
    }
}