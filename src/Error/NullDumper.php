<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error;

use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Log\DebugLogger;

class NullDumper implements ErrorDumperInterface
{
    public function dump(ErrorHandlerInterface $errorHandler, DebugLogger $debugLogger): ?string
    {
        return null;
    }
}
