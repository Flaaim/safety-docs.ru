<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\UnsubscribeContact;

use App\Distribution\Entity\Project\Contact;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private ProjectRepository $projects;
    private string $apiKey;
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
        $container = $this->app()->getContainer();

        $this->projects = $container->get(ProjectRepository::class);
        $this->apiKey = $container->get('config')['uniSender']['apiKey'];
    }

    public function testSetWebHook(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/distributions/projects/unsubscribe'));
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/projects/unsubscribe'));
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testSuccess(): void
    {

        $events = [
            [
                'user_id' => 456,
                'events' => [
                    [
                        'event_name' => 'transactional_email_status',
                        'event_data' => [
                            'email' => 'one@mail.ru',
                            'status' => 'unsubscribed',
                        ],
                    ],
                ],
            ],
        ];
        $request = $this->createValidWebhookRequest($events);

        $response = $this->app()->handle($request);

        self::assertEquals(200, $response->getStatusCode());

        $project = $this->projects->findById(new ProjectId(RequestFixture::PROJECT_ID));
        /** @var array<Contact> $contacts */
        $contacts = $project->getSubscribedContacts();
        self::assertCount(1, $contacts);

    }

    private function createValidWebhookRequest(array $eventsByUser): ServerRequestInterface
    {
        $initialData = [
            'auth' => $this->apiKey,
            'events_by_user' => $eventsByUser,
        ];
        $jsonStringWithKey = json_encode($initialData);
        if(!$jsonStringWithKey) {
            throw new \RuntimeException('Invalid JSON string');
        }
        $validHash = md5($jsonStringWithKey);
        $finalRawBody = str_replace($this->apiKey, $validHash, $jsonStringWithKey);
        $finalParsedBody = json_decode($finalRawBody, true);

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/v1/distributions/projects/unsubscribe');

        $request->getBody()->write($finalRawBody);
        $request->getBody()->rewind();

        return $request->withParsedBody($finalParsedBody);
    }

}