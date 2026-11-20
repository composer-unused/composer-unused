<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;

final class StaticStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof StaticCall) {
            return false;
        }

        if (!$node->class instanceof Node\Name) {
            return false;
        }

        return $node->class->isFullyQualified() || $node->class->isQualified();
    }

    /**
     * @param Node&StaticCall $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var Node\Name $class */
        $class = $node->class;

        return [$class->toString()];
    }
}
