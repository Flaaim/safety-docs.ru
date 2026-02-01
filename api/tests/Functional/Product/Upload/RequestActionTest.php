<?php

namespace Test\Functional\Product\Upload;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    use ArraySubsetAsserts;

    private array $tempFiles = [];
    public function testSuccess(): void
    {
        $uploadedFile = $this->buildUploadedFile('test.docx', 'data', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/payment-service/products/upload',
            ['path' => 'fire/pb992'],
            ['file' => $uploadedFile]
        ));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());
        
        $data = Json::decode($body);

        self::assertArraySubset([
            'name' => $uploadedFile->getClientFilename(),
            'mime_type' => $uploadedFile->getClientMediaType(),
            'size' => $uploadedFile->getSize(),
            'path' => '/tmp/fire/pb992/'.$uploadedFile->getClientFilename(),
        ], $data);
        
    }

    public function testEmptyFile(): void
    {

        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'fire/pb992']));

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
        $tempFileOne = $this->buildUploadedFile('test1.docx', 'some_content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $tempFileTwo = $this->buildUploadedFile('test2.docx', 'some_content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'fire/pb992'], ['file' => [$tempFileOne, $tempFileTwo]
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
        $file = $this->buildUploadedFile('test1.docx', 'some_content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($tempFile, 'test content');

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

        $uploadedFile = $this->buildUploadedFile('test.docx', 'data', $invalidFileType = 'text/plain', UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData(
            'POST', '/payment-service/products/upload', ['path' => 'fire/pb992'], ['file' => $uploadedFile])
        );

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'uploadFile' => 'The mime type of the file is invalid (text/plain). Allowed mime types are application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf.'
            ]
        ], $data);
    }

    public function testUploadFailed(): void
    {

        $uploadedFile = $this->buildUploadedFile('test', 'data',  'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_NO_FILE);
        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'fire/pb992'], ['file' => $uploadedFile]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertArraySubset(['errors' => [
            'uploadFile' => 'Upload file required.',
        ]], $data);
    }

    public function testUploadExisting(): void
    {
        $uploadedFile = $this->buildUploadedFile('test.docx', 'data', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',UPLOAD_ERR_OK);
        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'fire/pb992'], ['file' => $uploadedFile]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());

        $data = Json::decode($body);

        self::assertEquals('data', file_get_contents($data['path']));

        $uploadedFile = $this->buildUploadedFile('test.docx', 'data2', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/payment-service/products/upload', ['path' => 'fire/pb992992'], ['file' => $uploadedFile]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = $response->getBody());
        $data = Json::decode($body);

        self::assertEquals('data2', file_get_contents($data['path']));

    }
    private function buildUploadedFile(string $name, string $content, string $type, int $error): UploadedFileInterface
    {
        $tempFile = tempnam(sys_get_temp_dir(), $name);
        file_put_contents($tempFile, $content);
        $this->tempFiles[] = $tempFile;

        return new UploadedFile(
            $tempFile,
            $name,
            $type,
            filesize($tempFile),
            $error
        );
    }

    public function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}