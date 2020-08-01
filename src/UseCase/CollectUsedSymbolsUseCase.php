<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase;

use Composer\Composer;
use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Symbol\Loader\SymbolLoaderInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

use function array_keys;
use function array_merge;
use function strpos;

class CollectUsedSymbolsUseCase
{
    /** @var SymbolLoaderInterface */
    private $usedSymbolLoader;

    public function __construct(SymbolLoaderInterface $usedSymbolLoader)
    {
        $this->usedSymbolLoader = $usedSymbolLoader;
    }

    /**
     * @return Generator<string, SymbolInterface>
     */
    public function execute(Composer $composer): Generator
    {
        $rootPackage = $composer->getPackage();
        $baseDir = dirname($composer->getConfig()->getConfigSource()->getName());

        $usedSymbols = $this->usedSymbolLoader->load(
            PackageDecorator::withBaseDir(
                $baseDir,
                $rootPackage
            )
        );

        $rootNamespaces = array_merge(
            array_keys($rootPackage->getAutoload()['psr-0'] ?? []),
            array_keys($rootPackage->getAutoload()['psr-4'] ?? [])
        );

        yield from $this->filterRootPackageSymbols($rootNamespaces, $usedSymbols);
    }

    /**
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
                    continue;
                }

                yield $identifier => $symbol;
            }
        }
    }
}
