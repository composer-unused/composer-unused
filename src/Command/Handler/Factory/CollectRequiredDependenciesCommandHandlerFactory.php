<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler\Factory;

use ComposerUnused\ComposerUnused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\PackageResolver;
use ComposerUnused\ComposerUnused\Symbol\ProvidedSymbolLoaderBuilder;
use Psr\Container\ContainerInterface;

final class CollectRequiredDependenciesCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): CollectRequiredDependenciesCommandHandler
    {
        return new CollectRequiredDependenciesCommandHandler(
            $container->get(PackageResolver::class),
            $container->get(ProvidedSymbolLoaderBuilder::class)
        );
    }
}
