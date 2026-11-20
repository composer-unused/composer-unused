<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use ArrayIterator;
use Closure;
use ComposerUnused\SymbolParser\Symbol\Provider\FileIterationInterface;
use Generator;
use PhpParser\ErrorHandler;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use SplFileInfo;

final class SymbolNameParser implements SymbolNameParserInterface
{
    private Parser $parser;
    private NodeTraverser $traverser;
    private SymbolCollectorInterface $visitor;
    private ErrorHandler $errorHandler;
    private ?FileIterationInterface $fileIterator = null;
    private ?SplFileInfo $currentFile = null;

    public function __construct(Parser $parser, NameResolver $nameResolver, SymbolCollectorInterface $visitor, ?ErrorHandler $errorHandler = null)
    {
        $this->parser = $parser;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($nameResolver);
        $this->traverser->addVisitor($visitor);

        $this->visitor = $visitor;
        $this->errorHandler = $errorHandler ?? new ParserErrorCollector();
    }

    /**
     * @return Generator<string>
     */
    public function parseSymbolNames(string $code): Generator
    {
        $nodes = $this->parser->parse($code, $this->errorHandler);

        if ($nodes === null) {
            return;
        }

        if ($this->fileIterator !== null) {
            $this->visitor->setFileIncludeCallback(Closure::fromCallable([$this, 'handleFileInclude']));
        }

        $this->traverser->traverse($nodes);

        yield from $this->visitor->getSymbolNames();
        $this->visitor->reset();
    }

    public function setFileIterator(FileIterationInterface $fileIterator): void
    {
        $this->fileIterator = $fileIterator;
    }

    public function setCurrentFile(SplFileInfo $file): void
    {
        $this->currentFile = $file;
    }

    public function handleFileInclude(FileInclude $fileInclude): void
    {
        if ($this->fileIterator === null || $this->currentFile === null) {
            return;
        }

        $file = new SplFileInfo($this->currentFile->getPath() . '/' . ltrim($fileInclude->getPath(), '/'));
        $this->fileIterator->appendFiles(new ArrayIterator([$file]));
    }
}
