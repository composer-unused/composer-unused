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

    private ?string $dependenciesDir = null;

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
        if (array_key_exists($dependencyName, $this->additionalFiles)) {
            throw new AdditionalFilesAlreadySetException(
                'You already added files for ' . $dependencyName . '. Did you want to add multiple files? Try adding these via multiple globs.'
            );
        }

        $this->additionalFiles[$dependencyName] = $files;
        return $this;
    }

    public function setDependenciesDir(string $dependenciesDir): self
    {
        $this->dependenciesDir = $dependenciesDir;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDependenciesDir(): ?string
    {
        return $this->dependenciesDir;
    }
}
