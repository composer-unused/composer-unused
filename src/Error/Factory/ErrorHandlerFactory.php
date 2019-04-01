<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Factory;

use Icanhazstring\Composer\Unused\Error\ErrorHandler;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandlerInterface
    {
        return new ErrorHandler($container->get(LoggerInterface::class));
    }
}
