<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;

use function array_merge;

final class FilterDependencyCollectionCommand
{
    private const GLOBAL_NAMED_EXCLUSION = [
        'composer-plugin-api'
    ];

    private const GLOBAL_PATTERN_EXCLUSION = [
        '/-implementation$/i'
    ];

    /** @var DependencyCollection */
    private $requiredDependencyCollection;
    /** @var array<string> */
    private $namedExclusion;
    /** @var array<string> */
    private $patternExclusion;

    /**
     * @param DependencyCollection $requiredDependencyCollection
     * @param array<string> $namedExclusion
     * @param array<string> $patternExclusion
     */
    public function __construct(
        DependencyCollection $requiredDependencyCollection,
        array $namedExclusion = [],
        array $patternExclusion = []
    ) {
        $this->requiredDependencyCollection = $requiredDependencyCollection;
        $this->namedExclusion = array_merge(self::GLOBAL_NAMED_EXCLUSION, $namedExclusion);
        $this->patternExclusion = array_merge(self::GLOBAL_PATTERN_EXCLUSION, $patternExclusion);
    }

    public function getRequiredDependencyCollection(): DependencyCollection
    {
        return $this->requiredDependencyCollection;
    }

    /**
     * @return array<string>
     */
    public function getNamedExclusion(): array
    {
        return $this->namedExclusion;
    }

    /**
     * @return array<string>
     */
    public function getPatternExclusion(): array
    {
        return $this->patternExclusion;
    }
}
