<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

use function Sentry\captureException;
/** @psalm-suppress UnusedClass */
final class SentryDecorator implements ErrorHandlerInterface
{
    public function __construct(
        private readonly ErrorHandlerInterface $handler
    ) {
    }
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        captureException($exception);
        return $this->handler->__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
    }
}
