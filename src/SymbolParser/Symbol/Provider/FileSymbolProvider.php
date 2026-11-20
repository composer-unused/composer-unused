<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Provider;

use AppendIterator;
use ComposerUnused\SymbolParser\Exception\IOException;
use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParserInterface;
use ComposerUnused\SymbolParser\Symbol\Symbol;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use Generator;
use Iterator;

class FileSymbolProvider implements FileIterationInterface
{
    private SymbolNameParserInterface $parser;
    private FileContentProvider $fileContentProvider;
    /* @phpstan-ignore-next-line */
    private AppendIterator $fileIterator;

    public function __construct(SymbolNameParserInterface $parser, FileContentProvider $fileContentProvider)
    {
        $this->fileIterator = new AppendIterator();
        $this->parser = $parser;
        $this->parser->setFileIterator($this);
        $this->fileContentProvider = $fileContentProvider;
    }

    /**
     * @return Generator<string, SymbolInterface>
     * @throws IOException
     */
    public function provide(): Generator
    {
        foreach ($this->fileIterator as $file) {
            try {
                $content = $this->fileContentProvider->getContent($file);
                $this->parser->setCurrentFile($file);

                foreach ($this->parser->parseSymbolNames($content) as $symbolName) {
                    yield $symbolName => new Symbol($symbolName);
                }
            } catch (IOException $exception) {
                // TODO add logging
                continue;
            }
        }

        $this->fileIterator = new AppendIterator();
    }

    public function appendFiles(Iterator $files): void
    {
        $this->fileIterator->append($files);
    }
}
