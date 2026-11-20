<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;

/**
 * A strategy to extract symbol names from a node while traversing the AST
 */
interface StrategyInterface
{
    public function canHandle(Node $node): bool;

    /**
     * @param Node $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array;
}
