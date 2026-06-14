<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\UnsubscribeMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Test\Functional\Json;

final class UnsubscribeMiddlewareTest extends TestCase
{
    private LoggerInterface $logger;
    private ContainerInterface $container;
    private UnsubscribeMiddleware $middleware;
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);

        $this->middleware = new UnsubscribeMiddleware($this->container, $this->logger);
    }

    public function testNoAuth(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/unsubscribe');

        $this->container->expects(self::once())
            ->method('get')->willReturn(['uniSender' => ['apiKey' => 'test-auth']]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::never())->method('handle');

        $response = $this->middleware->process($request, $handler);

        self::assertEquals(403, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());
        $data = Json::decode($body);


        self::assertEquals(['error' => 'Forbidden'], $data);
    }

    public function testAuthTokenInvalid(): void
    {
        $request = $this->createValidWebhookRequest([], 'hacker-key');

        $this->container->expects(self::once())
            ->method('get')->willReturn(['uniSender' => ['apiKey' => 'test-auth']]);

        $this->logger->expects(self::once())
            ->method('error')->with(self::equalTo('Auth in unsubscribed request invalid.'));

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::never())->method('handle');

        $response = $this->middleware->process($request, $handler);

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals([
            'error' => 'Forbidden',
        ], $data);
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
                            'email' => 'bad-recipient@example.com',
                            'status' => 'unsubscribed',
                        ],
                    ],
                ],
            ],
        ];

        $request = $this->createValidWebhookRequest($events, 'test-auth');

        $this->container->expects(self::once())
            ->method('get')->willReturn(['uniSender' => ['apiKey' => 'test-auth']]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects(self::once())->method('handle')->willReturnCallback(
            static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals(['bad-recipient@example.com'], $request->getParsedBody());
                return (new ResponseFactory())->createResponse();
            }
        );

        $this->middleware->process($request, $handler);
    }

    public function testSpamBlock(): void
    {

        $events = [
            [
                'user_id' => 456,
                'events' => [
                    [
                        'event_name' => 'transactional_spam_block',
                        'event_data' => [
                            'job_id' => '1a3Q2V-0000OZ-S0',
                            'metadata' => [
                                'block_time' => '2015-11-30 15:09:42',
                                'block_type' => 'one_smtp',
                                'domain' => 'domain.com',
                                "SMTP_blocks_count" => 8,
                                "domain_status" => "blocked",
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $request = $this->createValidWebhookRequest($events);

        $this->container->expects(self::once())
            ->method('get')->willReturn(['uniSender' => ['apiKey' => 'test-auth']]);


        $this->logger->expects(self::once())->method('critical')
            ->with(
                self::equalTo('ВНИМАНИЕ! UniSender заблокировал отправку (Spam Block)!'),
                self::equalTo(['event_data' => [
                    'job_id' => '1a3Q2V-0000OZ-S0',
                    'metadata' => [
                        'block_time' => '2015-11-30 15:09:42',
                        'block_type' => 'one_smtp',
                        'domain' => 'domain.com',
                        "SMTP_blocks_count" => 8,
                        "domain_status" => "blocked",
                    ],
                ]])
            );
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects(self::once())->method('handle')->willReturnCallback(
            static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals([], $request->getParsedBody());
                return (new ResponseFactory())->createResponse();
            }
        );

        $this->middleware->process($request, $handler);
    }

    private function createValidWebhookRequest(array $eventsByUser, string $apiKey = 'test-auth'): ServerRequestInterface
    {
        $initialData = [
            'auth' => $apiKey,
            'events_by_user' => $eventsByUser,
        ];
        $jsonStringWithKey = json_encode($initialData);
        $validHash = md5($jsonStringWithKey);
        $finalRawBody = str_replace($apiKey, $validHash, $jsonStringWithKey);
        $finalParsedBody = json_decode($finalRawBody, true);

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/unsubscribe');

        $request->getBody()->write($finalRawBody);
        $request->getBody()->rewind();

        return $request->withParsedBody($finalParsedBody);
    }
}