<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
