<?php

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\DomainExceptionHandler;
use DomainException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Test\Functional\Json;

class DomainExceptionHandlerTest extends TestCase
{
    public function testNormal(): void
    {
        $middleware = new DomainExceptionHandler($logger = $this->createMock(LoggerInterface::class));

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test');
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')
            ->willReturn($response = $this->createMock(ResponseInterface::class));


        self::assertEquals($response, $middleware->process($request, $handler));
    }

    public function testException(): void
    {
        $middleware = new DomainExceptionHandler($logger = $this->createMock(LoggerInterface::class));
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test');
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')->willThrowException(new DomainException('some error message'));

        $response = $middleware->process($request, $handler);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['message' => 'some error message'], $data);
    }
}