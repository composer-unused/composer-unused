<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler;

use ComposerUnused\ComposerUnused\Command\FilterDependencyCollectionCommand;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;

final class CollectFilteredDependenciesCommandHandler
{
    public function collect(FilterDependencyCollectionCommand $command): DependencyCollection
    {
        $filters = $command->getFilters();

        return $command->getRequiredDependencyCollection()->filter(
            static function (DependencyInterface $dependency) use ($filters) {
                foreach ($filters as $filter) {
                    if ($filter->applies($dependency)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
