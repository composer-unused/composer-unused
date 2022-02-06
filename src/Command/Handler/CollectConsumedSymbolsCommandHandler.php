<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler;

use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use Generator;
use ComposerUnused\ComposerUnused\Command\CollectConsumedSymbolsCommand;
use ComposerUnused\ComposerUnused\Symbol\ConsumedSymbolLoaderBuilder;

use function array_keys;
use function array_merge;
use function strpos;

final class CollectConsumedSymbolsCommandHandler
{
    private ConsumedSymbolLoaderBuilder $consumedSymbolLoaderBuilder;

    public function __construct(ConsumedSymbolLoaderBuilder $consumedSymbolLoaderBuilder)
    {
        $this->consumedSymbolLoaderBuilder = $consumedSymbolLoaderBuilder;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function collect(CollectConsumedSymbolsCommand $command): Generator
    {
        $package = $command->getPackage();
        $symbolLoader = $this->consumedSymbolLoaderBuilder->build();

        $rootNamespaces = array_merge(
            array_keys($package->getAutoload()['psr-0'] ?? []),
            array_keys($package->getAutoload()['psr-4'] ?? [])
        );

        yield from $this->filterRootPackageSymbols(
            $rootNamespaces,
            $symbolLoader->withBaseDir($command->getPackageRoot())->load($package)
        );
    }

    /**
     * Ignore symbols that are provided and used by the root namespace.
     *
     * @param iterable<string> $rootNamespaces
     * @param iterable<string, SymbolInterface> $symbols
     *
     * @return Generator<string, SymbolInterface>
     */
    private function filterRootPackageSymbols(iterable $rootNamespaces, iterable $symbols): Generator
    {
        foreach ($symbols as $identifier => $symbol) {
            foreach ($rootNamespaces as $rootNamespace) {
                if (strpos($symbol->getIdentifier(), $rootNamespace) === 0) {
                    continue 2;
                }
            }

            yield $identifier => $symbol;
        }
    }
}
