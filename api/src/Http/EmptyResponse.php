<?php

namespace App\Http;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;

class EmptyResponse extends Response
{
    /**
     *@psalm-suppress PossiblyFalseArgument
     */
    public function __construct($status = 204)
    {
        parent::__construct(
            $status,
            null,
            (new StreamFactory())->createStream('')
        );
    }
}
