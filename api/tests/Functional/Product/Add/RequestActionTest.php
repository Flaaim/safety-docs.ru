<?php

namespace Test\Functional\Product\Add;

use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystem;
    public function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = InMemoryFileSystemPath::create();
    }
    public function testSuccess(): void
    {
        $file = $this->createUploadFile('med100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            $this->getProductData('success-product'),
            ['file' => $file]
        ));

        $this->assertEquals(201, $response->getStatusCode());
    }
    public function testEmpty(): void
    {
        $file = $this->createUploadFile('fire100.1.txt', 'test content', 'text/plain', UPLOAD_ERR_OK);
        $response = $this->app()->handle(
            self::formData(
                'POST',
                '/v1/products',
                [],
                ['file' => $file]
            ));

        $this->assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'name' => 'This value is too short. It should have 5 characters or more.',
                'cipher' => 'This value should not be blank.',
                'amount' => 'This value should be positive.',
                'slug' => 'This value should not be blank.',
                'updatedAt' => 'This value should not be blank.',
                'file' => 'The extension of the file is invalid (txt). Allowed extensions are rar.',
                'totalDocuments' => 'This value should be greater than 0.',
                'formatDocuments' => 'This value should not be blank.',
            ]
        ], $data);
    }
    public function testSlugExist(): void
    {
        $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            $this->getProductData('slug-exist'),
            ['file' => $this->createUploadFile('electr100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK)]
        ));

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            $this->getProductData('slug-exist'),
            ['file' => $this->createUploadFile('electr100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK)]
        ));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product with slug slug-exist already exists.'
        ], $data);
    }

    public function testEmptyFileField(): void
    {
        $response = $this->app()->handle(
            self::formData('POST', '/v1/products', $this->getProductData())
        );

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'File is required.'
        ], $data);

    }

    private function getProductData(string $slug = 'product-slug-1'): array
    {
        return [
            'name' => 'Электробезопасность 1 группа',
            'cipher' => 'serv100.1',
            'amount' => 500.00,
            'slug' => $slug,
            'updatedAt' => '2019-01-01',
            'totalDocuments' => 10,
            'formatDocuments' => ['docx', 'pdf'],
        ];
    }
    private function createUploadFile(string $name, string $content, string $type, int $error): UploadedFileInterface
    {
        $file1 = tempnam($this->fileSystem->getValue(), 'file1');
        $result = file_put_contents($file1, $content);
        if(!$result){
            throw new \RuntimeException('Unable to write file');
        }
        return new UploadedFile(
            $file1,
            $name,
            $type,
            filesize($file1),
            $error,
        );
    }
    public function tearDown(): void
    {
        $this->fileSystem->clear();
    }
}