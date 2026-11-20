<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

final class ExtendsParseStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof Class_) {
            return false;
        }

        if (!$node->extends instanceof Node\Name) {
            return false;
        }

        return true;
    }

    /**
     * @param Node&Class_ $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var Node\Name $extends */
        $extends = $node->extends;

        return [$extends->toString()];
    }
}
