<?php

namespace Test\Functional\Product\Images\Clear;

use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use org\bovigo\vfs\vfsStream;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystemPath;
    private ProductRepository $products;
    public function setUp(): void
    {
        $this->fileSystemPath = InMemoryFileSystemPath::create();
        $this->loadFixtures([RequestFixture::class]);
        $this->products = $this->container->get(ProductRepository::class);
    }

    public function testSuccess(): void
    {
        $root = vfsStream::setup('storage');
        $productId = 'b38e76c0-ac23-4c48-85fd-975f32c8801f';
        vfsStream::newFile($productId . '/image1.jpg')->at($root)->setContent('some content');

        $path = vfsStream::url('storage/' . $productId . '/image1.jpg');

        self::assertFileExists($path);

        $response = $this->app()->handle(self::json(
            'DELETE', '/v1/products/'. $productId .'/images')
        );

        self::assertEquals(204, $response->getStatusCode());

        self::assertFileDoesNotExist($path);

        $product = $this->products->get(new ProductId($productId));
        self::assertEmpty($product->getImages());
    }

    public function testNotFound(): void
    {

        $response = $this->app()->handle(self::json(
            'DELETE', '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c88012/images')
        );

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);
    }

    public function tearDown(): void
    {
        $this->fileSystemPath->clear();
    }
}