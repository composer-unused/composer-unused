<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface
{
    public function handleError(Throwable $error): void;
}
