<?php

declare(strict_types=1);

namespace Distribution\Project\Delete;

use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use Test\Functional\Distribution\Project\Delete\RequestFixture;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private readonly ProjectRepository $projects;
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
        $container = $this->app()->getContainer();
        $this->projects = $container->get(ProjectRepository::class);
    }

    public function testDelete(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/projects/'. RequestFixture::PROJECT_ID));

        self::assertEquals(204, $response->getStatusCode());

        self::assertFalse($this->projects->hasById(new ProjectId(RequestFixture::PROJECT_ID)));

    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/projects/31a5df6a-801b-44fb-bc59-bf5a756159fa'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Project not found.'], $data);
    }
}