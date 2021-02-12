<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\CompositeSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\ExtensionSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\PsrSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\ParserFactory;

final class ProvidedSymbolLoaderBuilder
{
    public function build(string $packageRoot): SymbolLoaderInterface
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new DefinedSymbolCollector()
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        return new CompositeSymbolLoader(
            [
                new ExtensionSymbolLoader(),
                new PsrSymbolLoader(),
                new FileSymbolLoader($packageRoot, $fileSymbolProvider, ['classmap', 'files'])
            ]
        );
    }
}
