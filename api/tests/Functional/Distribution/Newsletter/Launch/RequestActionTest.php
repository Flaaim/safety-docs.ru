<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Launch;

use App\Distribution\Entity\Newsletter\NewsletterId;
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

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters/launch'));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'newsletterId' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters/launch', [
            'newsletterId' => 'invalid',
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'newsletterId' => 'This is not a valid UUID.',
        ]], $data);
    }

    public function testNotFoundNewsletter(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters/launch', [
            'newsletterId' => RequestFixture::NEWSLETTER_NOT_FOUND_ID,
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());
        $data = Json::decode($body);

        self::assertEquals(['message' => 'Newsletter not found.'], $data);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters/launch', [
            'newsletterId' => RequestFixture::NEWSLETTER_ID,
        ]));

        self::assertEquals(204, $response->getStatusCode());

        $newsletter = $this->newsletters->findById(new NewsletterId(RequestFixture::NEWSLETTER_ID));

        self::assertEquals('completed', $newsletter->getStatus()->getValue());
    }
}