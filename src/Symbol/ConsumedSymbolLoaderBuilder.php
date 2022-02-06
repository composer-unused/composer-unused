<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Symbol;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\AutoloadType;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolCollectorInterface;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\Lexer\Emulative;
use PhpParser\ParserFactory;

final class ConsumedSymbolLoaderBuilder
{
    private SymbolCollectorInterface $consumedSymbolCollector;
    private Emulative $lexer;

    public function __construct(SymbolCollectorInterface $consumedSymbolCollector, Emulative $lexer)
    {
        $this->consumedSymbolCollector = $consumedSymbolCollector;
        $this->lexer = $lexer;
    }

    public function build(): SymbolLoaderInterface
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $this->lexer),
            $this->consumedSymbolCollector
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        return new FileSymbolLoader(
            $fileSymbolProvider,
            AutoloadType::all()
        );
    }
}
