<?php

namespace App\Http;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;

class EmptyResponse extends Response
{
    public function __construct($status = 204)
    {
        parent::__construct(
            $status,
            null,
            (new StreamFactory())->createStreamFromResource(fopen('php://temp', 'rb'))
        );
    }
}
