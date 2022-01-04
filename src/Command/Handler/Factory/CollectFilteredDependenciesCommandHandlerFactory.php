<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler\Factory;

use ComposerUnused\ComposerUnused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use Psr\Container\ContainerInterface;

final class CollectFilteredDependenciesCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): CollectFilteredDependenciesCommandHandler
    {
        return new CollectFilteredDependenciesCommandHandler();
    }
}
