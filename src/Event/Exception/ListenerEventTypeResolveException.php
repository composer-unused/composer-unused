<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event\Exception;

use ReflectionException;
use RuntimeException;

class ListenerEventTypeResolveException extends RuntimeException
{
    public static function fromReflectionException(ReflectionException $exception): self
    {
        return new self('Unable to resolve type from listener.', $exception->getCode(), $exception);
    }
}
