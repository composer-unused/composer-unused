<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;

final class InstanceofStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof Node\Expr\Instanceof_) {
            return false;
        }

        if (!$node->class instanceof Node\Name) {
            return false;
        }

        return $node->class->isFullyQualified() || $node->class->isQualified();
    }

    /**
     * @param Node&Node\Expr\Instanceof_ $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var Node\Name $class */
        $class = $node->class;

        return [
            $class->toString(),
        ];
    }
}
