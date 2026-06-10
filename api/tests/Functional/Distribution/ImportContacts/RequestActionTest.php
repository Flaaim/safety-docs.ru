<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\ImportContacts;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystem;
    private ProjectRepository $projects;
    public function setUp(): void
    {
        $this->fileSystem = InMemoryFileSystemPath::createReal();
        $this->loadFixtures([RequestFixture::class]);
        $container = $this->app()->getContainer();
        $this->projects = $container->get(ProjectRepository::class);
    }

    public function testSuccess(): void
    {
        $file = $this->fileSystem->getValue() .
            DIRECTORY_SEPARATOR . RequestFixture::FILE_ID .
            DIRECTORY_SEPARATOR . 'contacts.csv';
        $this->createCsvFile($file);

        self::assertFileExists($file);

        $response = $this->app()->handle(self::json('POST', '/v1/distributions/import-contacts', [
            'fileId' => RequestFixture::FILE_ID,
            'projectId' => RequestFixture::PROJECT_ID,
        ]));

        self::assertEquals(204, $response->getStatusCode());
        /** @var Project $project */
        $project = $this->projects->findById(new ProjectId(RequestFixture::PROJECT_ID));

        self::assertCount(2, $project->getContacts());
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/import-contacts'));
        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'fileId' => 'This value should not be blank.',
            'projectId' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/import-contacts', [
            'fileId' => 'invalid',
            'projectId' => 'invalid',
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'fileId' => 'This is not a valid UUID.',
            'projectId' => 'This is not a valid UUID.',
        ]], $data);
    }

    public function testFileNotExists(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/import-contacts', [
            'fileId' => RequestFixture::FILE_ID,
            'projectId' => RequestFixture::PROJECT_ID,
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'File not found in filesystem.'], $data);
    }
    private function createCsvFile(string $path): void
    {
        $csvContent = "Email;Name\n" .
            "test@email.ru;John Doe\n" .
            "second@email.ru;Jane Doe";
        $dirPath = dirname($path);
        if (!is_dir($dirPath)) {
            $dir = mkdir($dirPath, 0777, true);
            if ($dir === false) {
                throw new \RuntimeException('Unable to create directory ' . $dirPath);
            }
        }
        $result = file_put_contents($path, $csvContent);
        if (!$result) {
            throw new \RuntimeException('Failed to write csv file');
        }
    }

    public function tearDown(): void
    {
        $this->fileSystem->clear();
    }
}