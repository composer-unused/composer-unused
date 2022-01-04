<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;

final class FilterDependencyCollectionCommand
{
    private DependencyCollection $requiredDependencyCollection;
    private FilterCollection $filterCollection;

    public function __construct(
        DependencyCollection $requiredDependencyCollection,
        FilterCollection $filterCollection
    ) {
        $this->requiredDependencyCollection = $requiredDependencyCollection;
        $this->filterCollection = $filterCollection;
    }

    public function getRequiredDependencyCollection(): DependencyCollection
    {
        return $this->requiredDependencyCollection;
    }

    public function getFilters(): FilterCollection
    {
        return $this->filterCollection;
    }
}
