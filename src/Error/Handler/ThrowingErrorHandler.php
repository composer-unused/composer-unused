<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Handler;

use PhpParser\Error;
use Throwable;

class ThrowingErrorHandler implements ErrorHandlerInterface
{
    /**
     * Adapter for php-parser error handler.
     *
     * @param Error $error
     * @return void
     * @throws Throwable
     */
    public function handleError(Error $error): void
    {
        $this->handle($error);
    }

    public function handle(Throwable $error): void
    {
        throw $error;
    }

    public function hasErrors(): bool
    {
        return false;
    }

    public function getErrors(): array
    {
        return [];
    }
}
