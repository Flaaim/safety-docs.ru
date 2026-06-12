<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Draft;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters', [
            'subject' => 'Обновления на сайте',
            'templateId' => '716a3943-c68a-45ad-90c0-2136cfe22096',
            'projectId' => RequestFixture::PROJECT_ID,
        ]));

        self::assertEquals(204, $response->getStatusCode());
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