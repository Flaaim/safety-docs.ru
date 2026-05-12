<?php

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\RouteMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Routing\RouteContext;

class RouteMiddlewareTest extends TestCase
{
    public function testNormal(): void
    {
        $middleware = new RouteMiddleware();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/test')
            ->withAttribute(RouteContext::ROUTE, $route = $this->createMock(RouteInterface::class));

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request) use ($route) {
                self::assertEquals($route, $request->getAttribute(RouteContext::ROUTE));
                self::assertEquals($route, $request->getAttribute('active_route'));
                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }

    public function testFailed(): void
    {
        $middleware = new RouteMiddleware();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/test')
            ->withAttribute(RouteContext::ROUTE, null);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Route not found in request context.');
        $middleware->process($request, $handler);
    }
}