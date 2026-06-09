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
        $this->fileSystemPath = InMemoryFileSystemPath::createReal();
        $this->loadFixtures([RequestFixture::class]);
        $this->products = $this->container->get(ProductRepository::class);
    }

    public function testSuccess(): void
    {
        $path = 'b38e76c0-ac23-4c48-85fd-975f32c8801f' . DIRECTORY_SEPARATOR . 'image1.jpg';
        $this->createFile($path);

        self::assertFileExists($this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $path);

        $response = $this->app()->handle(self::json(
            'DELETE', '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f/images')
        );

        self::assertEquals(204, $response->getStatusCode());

        self::assertFileDoesNotExist($path);

        $product = $this->products->get(new ProductId('b38e76c0-ac23-4c48-85fd-975f32c8801f'));
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
    private function createFile(string $path): void
    {
        $file = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $path;
        $dir = mkdir(dirname($file), 0777, true);
        if($dir === false) {
            throw new \RuntimeException('Unable to create directory');
        }
        $result = file_put_contents($file, 'some_content');
        if(!$result){
            throw new \RuntimeException('Unable to write file');
        }
    }
    public function tearDown(): void
    {
        $this->fileSystemPath->clear();
    }
}