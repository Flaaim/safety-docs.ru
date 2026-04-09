<?php

namespace Functional\Product\GetBySlug;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Test\Functional\Json;
use Test\Functional\Product\Get\RequestFixture;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    use ArraySubsetAsserts;
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/products/s/service'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'name' => 'Служба охраны труда',
            'formattedPrice' => '550.00 RUB',
            'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f',
            'updatedAt' => (new \DateTimeImmutable())->format('Y-m-d'),
            'cipher' => 'serv100.1',
            'filename' => 'serv100.1.rar',
            'slug' => 'service',
            'totalDocuments' => 10,
            'formatDocuments' => ['docx', 'pdf'],
        ], $data);
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/products/s/not-exist'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/products/s/124'));

        self::assertEquals(404, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset([
            'message' => '404 Not Found',
        ], $data);
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/products/s/'));

        self::assertEquals(404, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset([
            'message' => '404 Not Found',
        ], $data);
    }
}