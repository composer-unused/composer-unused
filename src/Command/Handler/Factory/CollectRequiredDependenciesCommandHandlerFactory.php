<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler\Factory;

use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\PackageResolver;
use Icanhazstring\Composer\Unused\Symbol\ProvidedSymbolLoaderBuilder;
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
