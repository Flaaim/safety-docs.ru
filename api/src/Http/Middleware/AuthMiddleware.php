<?php

namespace App\Http\Middleware;

use App\Http\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {}
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeader('Authorization');
        $token = str_replace('Bearer ', '', $token[0]);

        $expectedToken = $this->container->get('config')['auth']['api_token'];

        if($token !== $expectedToken) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ]);
        }
        return $handler->handle($request);
    }
}