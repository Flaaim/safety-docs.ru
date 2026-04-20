<?php

namespace App\Product\Command\Images\Clear;

class Command
{
    public function __construct(
        public string $productId,
    ){
    }
}