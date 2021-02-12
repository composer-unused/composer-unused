<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UsedExtensionSymbolStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\ParserFactory;
use Psr\Log\NullLogger;

use function get_loaded_extensions;

final class ConsumedSymbolLoaderBuilder
{
    public function build(string $packageRoot): SymbolLoaderInterface
    {
        $usedSymbolCollector = new ConsumedSymbolCollector(
            [
                new NewStrategy(),
                new StaticStrategy(),
                new UseStrategy(),
                new ClassConstStrategy(),
                new UsedExtensionSymbolStrategy(
                    get_loaded_extensions(),
                    // TODO logger
                    new NullLogger()
                )
            ]
        );

        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $usedSymbolCollector
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        return new FileSymbolLoader(
            $packageRoot,
            $fileSymbolProvider,
            ['classmap', 'files', 'psr-0', 'psr-4']
        );
    }
}
