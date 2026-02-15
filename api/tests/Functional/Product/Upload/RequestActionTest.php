<?php

namespace Test\Functional\Product\Upload;

use App\Product\Test\TempDir;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    use ArraySubsetAsserts;
    private TempDir $tempDir;
    public function setUp(): void
    {
        $this->tempDir = TempDir::create();
    }

    public function testSuccess(): void
    {
        $uploadedFile = $this->tempFile('serv100.1.rar', 'data', 'application/vnd.rar',UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/payment-service/products/upload',
            ['path' => 'safety/service'],
            ['file' => $uploadedFile]
        ));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());
        
        $data = Json::decode($body);

        self::assertArraySubset([
            'name' => $uploadedFile->getClientFilename(),
            'mime_type' => $uploadedFile->getClientMediaType(),
            'size' => $uploadedFile->getSize(),
            'path' => '/tmp/phpunit_test_/safety/service/'.$uploadedFile->getClientFilename(),
        ], $data);
    }

    public function testEmptyFile(): void
    {
        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'safety/service']));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'uploadFile' => 'Upload file required.',
            ]
        ], $data);
    }

    public function testMultiUpload(): void
    {
        $tempFileOne = $this->tempFile('test1.rar', 'some_content', 'application/vnd.rar', UPLOAD_ERR_OK);

        $tempFileTwo = $this->tempFile('test2.rar', 'some_content', 'application/vnd.rar', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => [$tempFileOne, $tempFileTwo]
            ]
        ));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = $response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset(['errors' => [
            'multipleFiles' => 'Only one uploaded file is allowed',
        ]], $data);
    }

    public function testEmptyPath(): void
    {
        $file = $this->tempFile('test1.rar', 'some_content', 'application/vnd.rar', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', [], [
            'file' => $file
        ]));
        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'targetPath' => 'This value should not be blank.',
            ]
        ], $data);
    }
    public function testInvalidMimeType(): void
    {
        $uploadedFile = $this->tempFile('test.rar', 'data', 'text/plain', UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => $uploadedFile])
        );

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'uploadFile' => 'The mime type of the file is invalid (text/plain). Allowed mime types are application/vnd.rar.'
            ]
        ], $data);
    }
    public function testInvalidExtension(): void
    {
        $uploadedFile = $this->tempFile('test.docx', 'data', 'application/vnd.rar', UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => $uploadedFile])
        );

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'uploadFile' => 'The extension of the file is invalid (docx). Allowed extensions are rar.'
            ]
        ], $data);
    }
    public function testUploadFailed(): void
    {
        $uploadedFile = $this->tempFile('test.rar', 'data',  'application/vnd.rar', UPLOAD_ERR_NO_FILE);

        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => $uploadedFile]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertArraySubset(['errors' => [
            'uploadFile' => 'Upload file required.',
        ]], $data);
    }

    public function testUploadExisting(): void
    {
        $uploadedFile = $this->tempFile('test.rar', 'data', 'application/vnd.rar',UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => $uploadedFile]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());

        $data = Json::decode($body);

        self::assertEquals('data', file_get_contents($data['path']));

        $uploadedFile = $this->tempFile('test.rar', 'data2', 'application/vnd.rar',UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'safety/service'], ['file' => $uploadedFile]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());
        $data = Json::decode($body);

        self::assertEquals('data2', file_get_contents($data['path']));

    }

    private function tempFile(string $name, string $content, string $type, int $error): UploadedFileInterface
    {
        $file1 = tempnam($this->tempDir->getValue(), 'file1');
        file_put_contents($file1, $content);

        return new UploadedFile(
            $file1,
            $name,
            $type,
            filesize($file1),
            $error
        );
    }
    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}