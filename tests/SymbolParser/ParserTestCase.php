<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test;

use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\NameResolver;
use ComposerUnused\SymbolParser\Parser\PHP\ParserErrorCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StrategyInterface;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class ParserTestCase extends TestCase
{
    /**
     * @param array<StrategyInterface> $strategies
     * @return array<string>
     */
    public function parseConsumedSymbols(array $strategies, string $code): array
    {
        $errorHandler = new ParserErrorCollector();

        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver($errorHandler),
            new ConsumedSymbolCollector($strategies),
            $errorHandler
        );

        return iterator_to_array($symbolNameParser->parseSymbolNames($code));
    }

    /**
     * @return array<string>
     */
    public function parseDefinedSymbols(string $code): array
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
        );

        return iterator_to_array($symbolNameParser->parseSymbolNames($code));
    }
}
