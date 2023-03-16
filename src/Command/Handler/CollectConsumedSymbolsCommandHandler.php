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
        $symbolLoader = $this
            ->consumedSymbolLoaderBuilder
            ->setAdditionalFiles($command->getConfiguration()->getAdditionalFilesFor($command->getPackage()->getName()))
            ->setExcludedDirs($command->getExcludedDirs())
            ->build();

        yield from $symbolLoader->withBaseDir($command->getPackageRoot())->load($command->getPackage());
    }
}
