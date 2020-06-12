<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Provider;

use Generator;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

class FunctionConstantSymbolProvider
{
    /** @var SymbolNameParser */
    private $parser;
    /** @var FileContentProvider */
    private $fileContentProvider;

    public function __construct(SymbolNameParser $parser, FileContentProvider $fileContentProvider)
    {
        $this->parser = $parser;
        $this->fileContentProvider = $fileContentProvider;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function provide(string $dir, array $files): Generator
    {
        foreach ($files as $file) {
            $content = $this->fileContentProvider->getContent($dir, $file);

            foreach ($this->parser->parseSymbolNames($content) as $symbolName) {
                yield new Symbol($symbolName);
            }
        }
    }
}
