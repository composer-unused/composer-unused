<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler\Factory;

use Icanhazstring\Composer\Unused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use Psr\Container\ContainerInterface;

final class CollectFilteredDependenciesCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): CollectFilteredDependenciesCommandHandler
    {
        return new CollectFilteredDependenciesCommandHandler();
    }
}
