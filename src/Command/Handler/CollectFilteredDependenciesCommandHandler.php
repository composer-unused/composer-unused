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
        $namedExclusion = $command->getNamedExclusion();
        $patternExclusion = $command->getPatternExclusion();

        return $command->getRequiredDependencyCollection()->filter(static function (DependencyInterface $dependency) use (
            $namedExclusion,
            $patternExclusion
        ) {
            if (in_array($dependency->getName(), $namedExclusion, true)) {
                return false;
            }

            foreach ($patternExclusion as $exclusion) {
                if (preg_match($exclusion, $dependency->getName())) {
                    return false;
                }
            }

            return true;
        });
    }
}
