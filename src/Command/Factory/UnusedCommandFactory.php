<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Factory;

use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Symbol\Loader\CompositeSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\ExtensionSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\PsrSymbolLoader;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UnusedCommandFactory
{
    public function __invoke(ContainerInterface $container): UnusedCommand
    {
        return new UnusedCommand(
            $container->get(ErrorHandlerInterface::class),
            new SymfonyStyleFactory(),
            $container->get(LoaderBuilder::class),
            $container->get(LoggerInterface::class),
            new CompositeSymbolLoader(
                [
                    $container->get(ExtensionSymbolLoader::class),
                    $container->get(PsrSymbolLoader::class),
                    $container->get(FileSymbolLoader::class)
                ]
            )
        );
    }
}
