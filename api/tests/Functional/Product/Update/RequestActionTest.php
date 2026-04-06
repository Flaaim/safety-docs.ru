<?php

namespace Test\Functional\Product\Update;


use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }
    public function testSuccessSameSlug(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f', $this->getProductData('service')));

        self::assertEquals(204, $response->getStatusCode());

        self::assertEquals('', $response->getBody()->getContents());
    }
    public function testSlugAlreadyExists(): void
    {
        $existingSlug = 'suot';
        $response = $this->app()->handle(self::formData('POST', '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f', $this->getProductData($existingSlug)));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product with this slug already exists.'
        ], $data);


    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f', $this->getProductData('fire')));

        self::assertEquals(204, $response->getStatusCode());

        self::assertEquals('', $response->getBody()->getContents());

    }
    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::formData('POST',
            '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8802f',
            $this->getProductData('service'))
        );

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product not found.',
        ], $data);
    }
    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products/invalid123',
            $this->getProductData('service')
        ));

        self::assertEquals(404, $response->getStatusCode());

    }

    private function getProductData(string $slug): array
    {
        return [
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f',
            'name' => 'Служба охраны труда',
            'cipher' => 'serv200.1',
            'amount' => 500.00,
            'path' => 'safety/service/serv200.1.rar',
            'slug' => $slug,
            'updatedAt' => '2025-01-01',
            'totalDocuments' => 10,
            'formatDocuments' => ['pdf', 'doc', 'docx'],
        ];
    }
}