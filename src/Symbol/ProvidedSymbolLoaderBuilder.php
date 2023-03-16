<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Symbol;

use ArrayIterator;
use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\AutoloadType;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\CompositeSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\ExtensionSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\PsrSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use SplFileInfo;

final class ProvidedSymbolLoaderBuilder
{
    private Emulative $lexer;
    /** @var array<SplFileInfo> */
    private array $additionalFiles = [];

    public function __construct(Emulative $lexer)
    {
        $this->lexer = $lexer;
    }

    public function build(): SymbolLoaderInterface
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $this->lexer),
            new NameResolver(),
            new DefinedSymbolCollector()
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        if (!empty($this->additionalFiles)) {
            $fileSymbolProvider->appendFiles(new ArrayIterator($this->additionalFiles));
        }

        return new CompositeSymbolLoader(
            [
                new ExtensionSymbolLoader(),
                new PsrSymbolLoader(),
                new FileSymbolLoader($fileSymbolProvider, [AutoloadType::CLASSMAP, AutoloadType::FILES])
            ]
        );
    }

    /**
     * @param array<string> $filesPaths
     */
    public function setAdditionalFiles(array $filesPaths): self
    {
        $this->additionalFiles = array_map(static fn(string $filePath) => new SplFileInfo($filePath), $filesPaths);

        return $this;
    }
}
