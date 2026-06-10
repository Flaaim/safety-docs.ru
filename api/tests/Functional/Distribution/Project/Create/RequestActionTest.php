<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\Create;

use App\Distribution\Entity\Project\ProjectRepository;
use Test\Functional\Json;
use Test\Functional\WebTestCase;
use function PHPUnit\Framework\assertEquals;

final class RequestActionTest extends WebTestCase
{
    private ProjectRepository $projects;
    public function setUp(): void
    {
        $container = $this->app()->getContainer();
        $this->projects = $container->get(ProjectRepository::class);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/projects', [
            'name' => 'Блог охраны труда'
        ]));

        assertEquals(201, $response->getStatusCode());

        self::assertTrue($this->projects->hasByName('Блог охраны труда'));
    }
    public function testExists(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/projects', [
            'name' => 'Блог охраны'
        ]));

        $response = $this->app()->handle(self::json('POST', '/v1/distributions/projects', [
            'name' => 'Блог охраны'
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Project with this name already exists.'], $data);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/projects'));

        self:assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
        ]], $data);
    }

}