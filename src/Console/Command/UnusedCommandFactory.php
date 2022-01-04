<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Command;

use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use Psr\Container\ContainerInterface;

final class UnusedCommandFactory
{
    public function __invoke(ContainerInterface $container): UnusedCommand
    {
        return new UnusedCommand(
            $container->get(ConfigFactory::class),
            $container->get(CollectConsumedSymbolsCommandHandler::class),
            $container->get(CollectRequiredDependenciesCommandHandler::class),
            $container->get(CollectFilteredDependenciesCommandHandler::class),
        );
    }
}
