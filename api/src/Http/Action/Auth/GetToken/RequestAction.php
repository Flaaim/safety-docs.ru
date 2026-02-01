<?php

namespace App\Http\Action\Auth\GetToken;

use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $config = $this->container->get('config')['auth'];

            if(isset($data['login']) && isset($data['password'])) {
                if($data['login'] === $config['login'] && $data['password'] === $config['password']) {
                    return new JsonResponse([
                        'token' => $config['api_token'],
                        'type' => 'Bearer'
                    ]);
                }
            }
            return new EmptyResponse(204);
        }catch (\Exception $e){
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}