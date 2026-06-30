<?php

namespace Test\Functional\Template\Category\Delete;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    private CategoryRepository $categories;
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);

        $this->categories = $this->container->get(CategoryRepository::class);
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('DELETE',
            '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/b5640c7c-75f8-41c3-96c1-0444e96b4f11'
        ));

        $this->assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.',
        ], $data);
    }

    public function testCanNotDelete(): void
    {
        $response = $this->app()->handle(self::json('DELETE',
        '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/8aa8f453-b19b-4b53-915b-1f04c83a9aee'));


        $this->assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category cannot be deleted. It has children.',
        ], $data);
        $category = $this->categories->findById(new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'));
        self::assertNotNull($category);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/directions/e42b8e4f-0ac3-4cca-984d-4f1dc983e970/categories/b5640c7c-75f8-41c3-96c1-0444e96b4f5d'));

        $this->assertEquals(204, $response->getStatusCode());

        $category = $this->categories->findById(new CategoryId('b5640c7c-75f8-41c3-96c1-0444e96b4f5d'));
        self::assertNull($category);

    }

}