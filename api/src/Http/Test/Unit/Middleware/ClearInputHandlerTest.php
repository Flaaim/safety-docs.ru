<?php

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\ClearInputHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;
use Slim\Psr7\UploadedFile;


class ClearInputHandlerTest extends TestCase
{
    public function testParsedBody(): void
    {
        $middleware = new ClearInputHandler();

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/test')->withParsedBody([
            'null' => null,
            'space' => ' ',
            'string' => 'String ',
            'nested' => [
                'null' => null,
                'space' => ' ',
                'name' => ' Name',
            ]
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals([
                    'null' => null,
                    'space' => '',
                    'string' => 'String',
                    'nested' => [
                        'null' => null,
                        'space' => '',
                        'name' => 'Name',
                    ]
                ], $request->getParsedBody());
                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);

    }

    public function testUploadedFiles(): void
    {
        $realFile = (new UploadedFileFactory())->createUploadedFile(
            (new StreamFactory())->createStream(''), 1, UPLOAD_ERR_OK,
        );
        $noFile = (new UploadedFileFactory())->createUploadedFile(
            (new StreamFactory())->createStream(''), 2, UPLOAD_ERR_NO_FILE,
        );
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/test')->withUploadedFiles([
            'realFile' => $realFile,
            'noFile' => $noFile,
            'arrayFiles' => [$realFile, $noFile]
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request) use ($realFile): ResponseInterface {
                self::assertEquals([
                    'realFile' => $realFile,
                    'arrayFiles' => [$realFile]
                ], $request->getUploadedFiles());

                return (new ResponseFactory())->createResponse();
            });

        $middleware = new ClearInputHandler();

        $middleware->process($request, $handler);
    }
}