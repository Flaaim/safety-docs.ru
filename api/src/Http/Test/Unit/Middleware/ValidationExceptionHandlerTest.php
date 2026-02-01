<?php

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\ValidationExceptionHandler;
use App\Http\Validator\ValidationException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;


class ValidationExceptionHandlerTest extends TestCase
{
    public function testValid(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test');
        $source = $this->createStub(Response::class);

        $middleware = new ValidationExceptionHandler();
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')->willReturn($source);

        $response = $middleware->process($request, $handler);

        self::assertEquals($source, $response);
    }

    public function testInvalid(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation('Invalid email', null, [],null, 'email', 'not-email'),
            new ConstraintViolation('Invalid product Id', null, [], null, 'productId', 'productId'),
        ]);
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test');
        $middleware = new ValidationExceptionHandler();

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')->willThrowException(new ValidationException(
            $violations
        ));

        $response = $middleware->process($request, $handler);

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'errors' => [
                'email' => 'Invalid email',
                'productId' => 'Invalid product Id',
            ]
        ], $data);
    }


}