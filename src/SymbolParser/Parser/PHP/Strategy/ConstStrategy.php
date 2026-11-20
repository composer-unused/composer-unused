<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;

final class ConstStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        return $node instanceof Node\Expr\ConstFetch;
    }

    /**
     * @param Node\Expr\ConstFetch $node
     */
    public function extractSymbolNames(Node $node): array
    {
        return [$node->name->getParts()[0]];
    }
}
