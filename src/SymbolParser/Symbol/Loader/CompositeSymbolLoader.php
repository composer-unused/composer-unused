<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Loader;

use ComposerUnused\Contracts\PackageInterface;
use Generator;

final class CompositeSymbolLoader implements SymbolLoaderInterface
{
    /** @var array<SymbolLoaderInterface> */
    private array $symbolLoader;

    /** @var string|null */
    private ?string $baseDir = null;

    /**
     * @param array<SymbolLoaderInterface> $symbolLoader
     */
    public function __construct(array $symbolLoader)
    {
        $this->symbolLoader = $symbolLoader;
    }

    public function load(PackageInterface $package): Generator
    {
        foreach ($this->symbolLoader as $loader) {
            yield from $loader->withBaseDir($this->baseDir)->load($package);
        }
    }

    public function withBaseDir(?string $baseDir): SymbolLoaderInterface
    {
        $clone = clone $this;
        $clone->baseDir = $baseDir;

        return $clone;
    }
}
