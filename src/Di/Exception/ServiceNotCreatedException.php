<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ServiceNotCreatedException extends RuntimeException implements ContainerExceptionInterface
{
}
