<?php

namespace App\Product\Test\Command\Update;

use App\Product\Command\Update\Command;
use App\Product\Command\Update\HandlerFactory;
use App\Product\Command\Update\WithFile\Handler as UpdateWithFileHandler;
use App\Product\Entity\ProductId;
use PHPUnit\Framework\TestCase;
use stdClass;

class HandlerFactoryTest extends TestCase
{
    private UpdateWithFileHandler $updateWithFileHandler;

    private HandlerFactory $factory;
    public function setUp(): void
    {
        $this->updateWithFileHandler = $this->createMock(UpdateWithFileHandler::class);
        $this->factory = new HandlerFactory([
            $this->updateWithFileHandler
        ]);
    }


    public function testInstance(): void
    {
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Handler not instance of UpdateProductHandlerInterface.');
        new HandlerFactory([new StdClass()]);
    }

    public function testCreateWithFile(): void
    {
        $command = $this->createCommand();

        $this->updateWithFileHandler->expects($this->once())->method('getType')
            ->willReturn(true);

        $this->updateWithFileHandler->expects(self::once())->method('handle')
            ->with($this->equalTo($command));

        $this->factory->createHandler($command, 'file');
    }

    public function testHandlerNotFound(): void
    {
        $command = $this->createCommand();

        $this->updateWithFileHandler->expects($this->once())->method('getType')
            ->willReturn(false);

        $this->updateWithFileHandler->expects(self::never())->method('handle');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Handler not found.');
        $this->factory->createHandler($command, 'notfound');
    }

    private function createCommand(): Command
    {
        return new Command(
            new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d'),
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            'test',
            (new \DateTimeImmutable())->format('d.m.Y'),
        );
    }
}