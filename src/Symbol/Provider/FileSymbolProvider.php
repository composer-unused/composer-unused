<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Provider;

use Generator;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParserInterface;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use SplFileInfo;

use const PHP_EOL;

class FileSymbolProvider
{
    /** @var SymbolNameParserInterface */
    private $parser;
    /** @var FileContentProvider */
    private $fileContentProvider;

    public function __construct(SymbolNameParserInterface $parser, FileContentProvider $fileContentProvider)
    {
        $this->parser = $parser;
        $this->fileContentProvider = $fileContentProvider;
    }

    /**
     * @param array<SplFileInfo> $files
     *
     * @return Generator<string, SymbolInterface>
     * @throws IOException
     */
    public function provide(?string $dir, iterable $files): Generator
    {
        foreach ($files as $file) {
            $content = $this->fileContentProvider->getContent($dir, $file);

            foreach ($this->parser->parseSymbolNames($content) as $symbolName) {
                yield $symbolName => new Symbol($symbolName);
            }
        }
    }
}
