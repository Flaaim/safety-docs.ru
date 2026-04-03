<?php

namespace App\Product\Command\Update;

class HandlerFactory
{

    public function __construct(
        /** @var array<UpdateProductHandlerInterface> */
        private readonly array $handlers
    ){
        foreach ($this->handlers as $handler) {
            if(!$handler instanceof UpdateProductHandlerInterface){
                throw new \DomainException('Handler not instance of UpdateProductHandlerInterface.');
            }
        }
    }

    public function createHandler(Command $command, string $type): void
    {
        foreach ($this->handlers as $handler) {
            if($handler->getType($type)) {
                $handler->handle($command);
                return;
            }
        }
        throw new \DomainException('Handler not found.');
    }
}