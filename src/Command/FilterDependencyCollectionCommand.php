<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;

use function array_merge;

final class FilterDependencyCollectionCommand
{
    private const GLOBAL_EXCLUSION = [
        'composer-plugin-api',
    ];

    /** @var DependencyCollection */
    private $requiredDependencyCollection;
    /** @var array<string> */
    private $namedExclusion;

    /**
     * @param DependencyCollection $requiredDependencyCollection
     * @param array<string> $namedExclusion
     */
    public function __construct(DependencyCollection $requiredDependencyCollection, array $namedExclusion)
    {
        $this->requiredDependencyCollection = $requiredDependencyCollection;
        $this->namedExclusion = array_merge(self::GLOBAL_EXCLUSION, $namedExclusion);
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
}
