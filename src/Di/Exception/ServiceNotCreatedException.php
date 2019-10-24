<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ServiceNotCreatedException extends RuntimeException implements ContainerExceptionInterface
{
}
