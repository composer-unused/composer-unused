<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\ErrorHandler;

use Throwable;

class ThrowingErrorHandler implements ErrorHandlerInterface
{
    /**
     * @param Throwable $error
     * @return void
     * @throws Throwable
     */
    public function handleError(Throwable $error): void
    {
        throw $error;
    }
}
