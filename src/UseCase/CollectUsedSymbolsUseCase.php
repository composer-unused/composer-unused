<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase;

use Composer\Package\RootPackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

use function array_keys;
use function array_merge;
use function strpos;

class CollectUsedSymbolsUseCase
{
    /** @var UsedSymbolLoader */
    private $usedSymbolLoader;

    public function __construct(UsedSymbolLoader $usedSymbolLoader)
    {
        $this->usedSymbolLoader = $usedSymbolLoader;
    }

    /**
     * @return Generator<string, SymbolInterface>
     */
    public function execute(RootPackageInterface $rootPackage, string $composerBaseDir): Generator
    {
        $usedSymbols = $this->usedSymbolLoader->load(
            PackageDecorator::withBaseDir(
                $composerBaseDir,
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
                    continue;
                }

                yield $identifier => $symbol;
            }
        }
    }
}
