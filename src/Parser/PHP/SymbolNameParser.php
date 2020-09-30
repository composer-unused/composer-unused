<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use Generator;
use PhpParser\NodeTraverser;
use PhpParser\Parser;

final class SymbolNameParser implements SymbolNameParserInterface
{
    /** @var Parser */
    private $parser;
    /** @var NodeTraverser */
    private $traverser;
    /** @var SymbolCollectorInterface */
    private $visitor;

    public function __construct(Parser $parser, SymbolCollectorInterface $visitor)
    {
        $this->parser = $parser;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($visitor);

        $this->visitor = $visitor;
    }

    /**
     * @return Generator<string>
     */
    public function parseSymbolNames(string $code): Generator
    {
        $nodes = $this->parser->parse($code);

        if ($nodes === null) {
            return;
        }

        $this->traverser->traverse($nodes);

        yield from $this->visitor->getSymbolNames();
        $this->visitor->reset();
    }
}
