<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error;

use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;

class NullDumper implements ErrorDumperInterface
{
    public function dump(ErrorHandlerInterface $errorHandler): ?string
    {
        return null;
    }
}
