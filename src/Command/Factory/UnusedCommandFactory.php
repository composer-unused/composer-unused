<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Factory;

use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Psr\Container\ContainerInterface;

class UnusedCommandFactory
{
    public function __invoke(ContainerInterface $container): UnusedCommand
    {
        return new UnusedCommand(
            $container->get(ErrorHandlerInterface::class),
            $container->get(ErrorDumperInterface::class),
            new SymfonyStyleFactory(),
            $container->get(LoaderBuilder::class),
            $container->get(DebugLogger::class),
            $container->get(IOInterface::class)
        );
    }
}
