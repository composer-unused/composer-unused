<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use ComposerUnused\SymbolParser\Symbol\Provider\FileIterationInterface;
use Generator;
use SplFileInfo;

interface SymbolNameParserInterface
{
    /**
     * @return Generator<string>
     */
    public function parseSymbolNames(string $code): Generator;

    /**
     * Set the current handling symbol provider to the parser for file include following
     */
    public function setFileIterator(FileIterationInterface $fileIterator): void;

    /**
     * Set current handles file
     */
    public function setCurrentFile(SplFileInfo $file): void;
}
