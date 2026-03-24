<?php

namespace Functional\Product\Add;

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
            [
                'name' => 'Медосмотры комплект документов',
                'cipher' => 'serv100.1',
                'amount' => 500.00,
                'path' => 'safety/medical/med100.1.rar',
                'slug' => 'medical',
                'updatedAt' => '01.01.2019',
            ],
            ['file' => $file]
        ));

        $this->assertEquals(201, $response->getStatusCode());

        self::assertFileExists('/tmp/phpunit_test_/safety/medical/med100.1.rar');
    }
    public function testInvalidNamesFile(): void
    {
        $file = $this->createUploadFile('med100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            [
                'name' => 'Медосмотры комплект документов',
                'cipher' => 'serv100.1',
                'amount' => 500.00,
                'path' => 'safety/firstaid/first100.1.rar',
                'slug' => 'medical',
                'updatedAt' => '01.01.2019',
            ],
            ['file' => $file]
        ));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($data = (string)$response->getBody());

        $data = Json::decode($data);

        self::assertEquals([
            'errors' => [
                'path' => 'Name of uploaded file and path is not equal.'
            ]
        ], $data);
    }
    public function testInvalid(): void
    {
        $file = $this->createUploadFile('fire100.1.txt', 'test content', 'text/plain', UPLOAD_ERR_OK);
        $response = $this->app()->handle(
            self::formData(
                'POST',
                '/v1/products',
                [
                    'name' => '',
                    'cipher' => '',
                    'amount' => 0,
                    'path' => '',
                    'slug' => '',
                    'updatedAt' => '',
                ],
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
                'path' => 'This value should not be blank.',
                'slug' => 'This value should not be blank.',
                'updatedAt' => 'This value should not be blank.',
                'file' => 'The extension of the file is invalid (txt). Allowed extensions are rar.'
            ]
        ], $data);
    }
    public function testSlugExist(): void
    {
        $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            $this->getProductData(),
            ['file' => $this->createUploadFile('electr100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK)]
        ));

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products',
            $this->getProductData(),
            ['file' => $this->createUploadFile('electr100.1.rar', 'test content', 'application/vnd.rar', UPLOAD_ERR_OK)]
        ));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Product with slug electrical already exists.'
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

    private function getProductData(): array
    {
        return [
            'name' => 'Электробезопасность 1 группа',
            'cipher' => 'serv100.1',
            'amount' => 500.00,
            'path' => 'safety/electrical/electr100.1.rar',
            'slug' => 'electrical',
            'updatedAt' => '01.01.2019',
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