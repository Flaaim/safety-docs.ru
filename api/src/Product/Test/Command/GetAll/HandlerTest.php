<?php

namespace App\Product\Test\Command\GetAll;

use App\Product\Command\GetAll\Command;
use App\Product\Command\GetAll\Handler;
use App\Product\Entity\DTO\ProductPaginatedDTOMapping;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Handler::class)]
class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $command = new Command(
            $page = 1,
            $perPage = 2
        );
        $handler = new Handler(
            $products = $this->createMock(ProductRepository::class),
            new ProductPaginatedDTOMapping()
        );

        $products->expects($this->once())->method('findAllPaginated')->willReturn([
            (new ProductBuilder())->withId(new ProductId('1c3202b2-c443-4afa-b47a-53927cf795c5'))->build(),
            (new ProductBuilder())->withId(new ProductId('7f9f8b03-4e77-46e1-a447-d4b3c1e46f59'))->build(),
        ]);

        $response = $handler->handle($command);

        self::assertEquals(2, $response->jsonSerialize()['total']);
        self::assertEquals($page, $response->jsonSerialize()['currentPage']);
        self::assertEquals($perPage, $response->jsonSerialize()['perPage']);
        self::assertEquals(1, $response->jsonSerialize()['totalPages']);
    }
}