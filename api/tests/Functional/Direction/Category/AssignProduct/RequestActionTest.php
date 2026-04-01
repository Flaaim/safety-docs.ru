<?php

namespace Test\Functional\Direction\Category\AssignProduct;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testAssignSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => '8192666c-03bb-4487-8d9f-08ea7cd4afec',
            'categoryId' => '15823c37-3358-44be-96dc-363d56bde91c',
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }
    public function testAssignProductExists(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => '8192666c-03bb-4487-8d9f-08ea7cd4afec',
            'categoryId' => '15823c37-3358-44be-96dc-363d56bde91c',
        ]));

        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => '8192666c-03bb-4487-8d9f-08ea7cd4afec',
            'categoryId' => '15823c37-3358-44be-96dc-363d56bde91c',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product already assigned. You must delete it first.',
        ], $data);
    }
    public function testAssignCategoryNotFound(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => '8192666c-03bb-4487-8d9f-08ea7cd4afec',
            'categoryId' => '15823c37-3358-44be-96dc-363d56bde911',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.',
        ], $data);
    }

    public function testAssignProductNotFound(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => '8192666c-03bb-4487-8d9f-08ea7cd4a222',
            'categoryId' => '15823c37-3358-44be-96dc-363d56bde91c',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);
    }

    public function testAssignInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign', [
            'productId' => 'invalid',
            'categoryId' => 'invalid',
        ]));

        self::assertEquals(422, $response->getStatusCode());
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/categories/assign'));


        $this->assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'productId' => 'This value should not be blank.',
                'categoryId' => 'This value should not be blank.',
            ]
        ], $data);
    }
}