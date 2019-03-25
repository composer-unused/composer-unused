<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Handler\Factory;

use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Error\Handler\CollectingErrorHandler;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Error\Handler\ThrowingErrorHandler;
use Psr\Container\ContainerInterface;

class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandlerInterface
    {
        /** @var IOInterface $io */
        $io = $container->get(IOInterface::class);

        return $io->isDebug()
            ? new CollectingErrorHandler()
            : new ThrowingErrorHandler();
    }
}
