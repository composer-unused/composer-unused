<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Symbol;

use ArrayIterator;
use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\AutoloadType;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolCollectorInterface;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use SplFileInfo;

final class ConsumedSymbolLoaderBuilder
{
    private SymbolCollectorInterface $consumedSymbolCollector;
    private Emulative $lexer;
    /** @var array<SplFileInfo> */
    private array $additionalFiles = [];
    /** @var list<string> */
    private array $excludedDirs;

    public function __construct(SymbolCollectorInterface $consumedSymbolCollector, Emulative $lexer)
    {
        $this->consumedSymbolCollector = $consumedSymbolCollector;
        $this->lexer = $lexer;
    }

    public function build(): SymbolLoaderInterface
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $this->lexer),
            new NameResolver(),
            $this->consumedSymbolCollector
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        if (!empty($this->additionalFiles)) {
            $fileSymbolProvider->appendFiles(new ArrayIterator($this->additionalFiles));
        }

        return new FileSymbolLoader(
            $fileSymbolProvider,
            AutoloadType::all(),
            $this->excludedDirs
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

    /**
     * @param list<string> $excludedDirs
     */
    public function setExcludedDirs(array $excludedDirs): self
    {
        $this->excludedDirs = $excludedDirs;

        return $this;
    }
}
