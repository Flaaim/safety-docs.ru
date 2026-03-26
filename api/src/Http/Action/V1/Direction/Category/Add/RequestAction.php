<?php

namespace App\Http\Action\V1\Direction\Category\Add;

use App\Direction\Command\Direction\Category\Add\Command;
use App\Direction\Command\Direction\Category\Add\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;


class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);

        $route = $routeContext->getRoute();

        $directionId = $route->getArgument('directionId', '');

        $title = $request->getParsedBody()['title'] ?? '';
        $description = $request->getParsedBody()['description'] ?? '';
        $text = $request->getParsedBody()['text'] ?? '';
        $slug = $request->getParsedBody()['slug'] ?? '';

        $command = new Command($directionId, $title, $description, $text, $slug);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }

}