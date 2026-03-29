<?php

namespace Test\Functional\Direction\Category\Update;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testDirectionNotFound(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e971/categories/8aa8f453-b19b-4b53-915b-1f04c83a9aee', [
            'title' => 'Инструктажи по охране труда',
            'description' => 'Описание инструктажей по охране труда',
            'text' => 'Текст для инструктаже по охране труда',
            'slug' => 'briefing',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Direction not found.',
        ], $data);
    }

    public function testCategoryNotFound(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/8aa8f453-b19b-4b53-915b-1f04c83a9ae1', [
            'title' => 'Инструктажи по охране труда',
            'description' => 'Описание инструктажей по охране труда',
            'text' => 'Текст для инструктаже по охране труда',
            'slug' => 'briefing',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.',
        ], $data);
    }

    public function testSuccessWithSameSlug(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/8aa8f453-b19b-4b53-915b-1f04c83a9aee', [
            'title' => 'Инструктажи по охране труда',
            'description' => 'Описание инструктажей по охране труда',
            'text' => 'Текст для инструктаже по охране труда',
            'slug' => 'service',
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }

    public function testSuccessWithNewSlug(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/8aa8f453-b19b-4b53-915b-1f04c83a9aee', [
            'title' => 'Инструктажи по охране труда',
            'description' => 'Описание инструктажей по охране труда',
            'text' => 'Текст для инструктаже по охране труда',
            'slug' => 'new-slug',
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }

    public function testSlugAlreadyTakenAnotherCategory(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/8aa8f453-b19b-4b53-915b-1f04c83a9aee', [
            'title' => 'Инструктажи по охране труда',
            'description' => 'Описание инструктажей по охране труда',
            'text' => 'Текст для инструктаже по охране труда',
            'slug' => 'education',
        ]));

        self::assertEquals(400, $response->getStatusCode());

    }
}