<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler;

use Icanhazstring\Composer\Unused\Command\FilterDependencyCollectionCommand;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;

final class CollectFilteredDependenciesCommandHandler
{
    public function collect(FilterDependencyCollectionCommand $command): DependencyCollection
    {
        $dependencyCollection = new DependencyCollection();
        $namedExclusion = $command->getNamedExclusion();

        foreach ($command->getRequiredDependencyCollection() as $dependency) {
            if (!in_array($dependency->getName(), $namedExclusion)) {
                $dependencyCollection->add($dependency);
            }
        }

        return $dependencyCollection;
    }
}
