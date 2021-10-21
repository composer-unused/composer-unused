<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler;

use Icanhazstring\Composer\Unused\Command\FilterDependencyCollectionCommand;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;

final class CollectFilteredDependenciesCommandHandler
{
    public function collect(FilterDependencyCollectionCommand $command): DependencyCollection
    {
        $namedExclusion = $command->getNamedExclusion();
        $patternExclusion = $command->getPatternExclusion();

        return $command->getRequiredDependencyCollection()->filter(static function ($dependency) use (
            $namedExclusion,
            $patternExclusion
        ) {
            if (in_array($dependency->getName(), $namedExclusion)) {
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
