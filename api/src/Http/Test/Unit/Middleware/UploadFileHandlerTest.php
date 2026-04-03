<?php

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\UploadFileHandler;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\UploadedFile;

class UploadFileHandlerTest extends TestCase
{

    public function testFileMissing(): void
    {
        $uploadFileHandler = new UploadFileHandler();

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test')->withUploadedFiles([]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle');

        $uploadFileHandler->process($request, $handler);
    }

    public function testHandle(): void
    {
        $uploadFileHandler = new UploadFileHandler();
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->method('getError')->willReturn(UPLOAD_ERR_OK);


        $request = (new ServerRequestFactory())->createServerRequest('POST', '/test')
            ->withUploadedFiles(['file' => $uploadedFile]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())->method('handle')
            ->with(self::callback(static function ($request) {
                self::assertInstanceOf(UploadedFileInterface::class, $request->getAttribute('target_file'));
                return true;
            }));

        $uploadFileHandler->process($request, $handler);
    }
}