<?php

namespace Test\Functional\Direction\Category\GetBySlug;

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
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/s/service')
        );

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'id' => '8aa8f453-b19b-4b53-915b-1f04c83a9aee',
            'title' => 'Служба охраны труда',
            'description' => 'Служба охраны труда - комплект документов',
            'text' => 'Some text',
            'slug' => 'service',
            'directionId' => 'e42b8e4f-0ac3-4cca-984d-4f1dc983e970',
            'directionTitle' => 'Охрана труда',
            'productId' => null,
            'productTitle' => null,
        ], $data);
    }
    public function testSuccessWithProduct(): void
    {
        $response = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/s/medical')
        );

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'id' => '040794de-7a19-47be-947a-e5ed74b579b8',
            'title' => 'Медицинские осмотры',
            'description' => 'Медицинские осмотры - комплект документов',
            'text' => 'Some text',
            'slug' => 'medical',
            'directionId' => 'e42b8e4f-0ac3-4cca-984d-4f1dc983e970',
            'directionTitle' => 'Охрана труда',
            'productId' => 'bffa46d9-6644-42d9-9c76-1e601c22d40b',
            'productTitle' => '5 документов медосмотров',
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