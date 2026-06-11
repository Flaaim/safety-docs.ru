<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Draft;

use App\Distribution\Entity\Newsletter\NewsletterRepository;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private readonly NewsletterRepository $newsletters;
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
        $container = $this->app()->getContainer();

        $this->newsletters = $container->get(NewsletterRepository::class);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters', [
            'subject' => 'Обновления на сайте',
            'templateId' => '716a3943-c68a-45ad-90c0-2136cfe22096',
            'projectId' => RequestFixture::PROJECT_ID,
        ]));

        self::assertEquals(204, $response->getStatusCode());

        $newsletters = $this->newsletters->findAll();

        self::assertEquals('Обновления на сайте', $newsletters[0]->getSubject());
        self::assertEquals('created', $newsletters[0]->getStatus()->getValue());
        self::assertEquals(RequestFixture::PROJECT_ID, $newsletters[0]->getProjectId()->getValue());
    }

    public function testProjectNotFound(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters', [
            'subject' => 'Обновления на сайте',
            'templateId' => '716a3943-c68a-45ad-90c0-2136cfe22096',
            'projectId' => 'da0b1ed0-255f-4a0a-bc6e-3d765adc9801',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Project not found.'], $data);
    }
    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters'));

        $this->assertSame(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'subject' => 'This value should not be blank.',
                'projectId' => 'This value should not be blank.',
                'templateId' => 'This value should not be blank.',
            ]
        ], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters', [
            'templateId' => 'invalid',
            'projectId' => 'invalid',
            'subject' => ''
        ]));
        $this->assertSame(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'templateId' => 'This is not a valid UUID.',
            'projectId' => 'This is not a valid UUID.',
            'subject' => 'This value should not be blank.'
        ]], $data);
    }
}