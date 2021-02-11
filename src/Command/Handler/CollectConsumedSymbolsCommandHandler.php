<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler;

use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use Generator;
use Icanhazstring\Composer\Unused\Command\CollectConsumedSymbolsCommand;
use function array_keys;
use function array_merge;
use function strpos;

final class CollectConsumedSymbolsCommandHandler
{
    /** @var ConsumedSymbolLoaderBuilder */
    private $symbolLoaderBuilder;

    public function __construct(ConsumedSymbolLoaderBuilder $symbolLoaderBuilder)
    {
        $this->symbolLoaderBuilder = $symbolLoaderBuilder;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function collect(CollectConsumedSymbolsCommand $command): Generator
    {
        $package = $command->getPackage();
        $symbolLoader = $this->symbolLoaderBuilder->build($command->getPackageRoot());

        $rootNamespaces = array_merge(
            array_keys($package->getAutoload()['psr-0'] ?? []),
            array_keys($package->getAutoload()['psr-4'] ?? [])
        );

        yield from $this->filterRootPackageSymbols(
            $rootNamespaces,
            $symbolLoader->load($package)
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
