<?php

namespace App\ErrorHandler;

use Slim\Handlers\ErrorHandler;

class LogErrorHandler extends ErrorHandler
{
    protected function writeToErrorLog(): void
    {
        $this->logger->error($this->exception->getMessage(), [
            'exception' => $this->exception,
            'url' => (string)$this->request->getUri(),
        ]);
    }
}
