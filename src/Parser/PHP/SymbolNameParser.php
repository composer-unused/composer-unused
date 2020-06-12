<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use Generator;
use PhpParser\NodeTraverser;
use PHPParser\Parser;

class SymbolNameParser
{
    /** @var Parser */
    private $parser;
    /** @var NodeTraverser */
    private $traverser;
    /** @var SymbolNodeVisitor */
    private $visitor;

    public function __construct(Parser $parser, SymbolNodeVisitor $visitor)
    {
        $this->parser = $parser;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($visitor);

        $this->visitor = $visitor;
    }

    /**
     * @return Generator<string>;
     */
    public function parseSymbolNames(string $code): Generator
    {
        $this->traverser->traverse($this->parser->parse($code));

        yield from $this->visitor->getFunctionNames();
        yield from $this->visitor->getConstantNames();
    }
}
