<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration;

final class Configuration
{
    /** @var array<string, NamedFilter> */
    private array $namedFilters = [];
    /** @var array<string, PatternFilter> */
    private array $patternFilters = [];
    /** @var array<string, array<string>> */
    private array $additionalFiles = [];

    public function addNamedFilter(NamedFilter $filter): self
    {
        $this->namedFilters[spl_object_hash($filter)] = $filter;
        return $this;
    }

    /**
     * @return array<string, NamedFilter>
     */
    public function getNamedFilters(): array
    {
        return $this->namedFilters;
    }

    public function addPatternFilter(PatternFilter $filter): self
    {
        $this->patternFilters[spl_object_hash($filter)] = $filter;
        return $this;
    }

    /**
     * @return array<string, PatternFilter>
     */
    public function getPatternFilters(): array
    {
        return $this->patternFilters;
    }

    /**
     * @return array<string>
     */
    public function getAdditionalFilesFor(string $dependencyName): array
    {
        return $this->additionalFiles[$dependencyName] ?? [];
    }

    /**
     * Set an additional list of files for composer-unused to parse for given dependency.
     *
     * @param array<string> $files
     */
    public function setAdditionalFilesFor(string $dependencyName, array $files): self
    {
        $this->additionalFiles[$dependencyName] = $files;
        return $this;
    }
}
