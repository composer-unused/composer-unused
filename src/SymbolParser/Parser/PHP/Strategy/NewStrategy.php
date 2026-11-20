<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;

final class NewStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof New_) {
            return false;
        }

        if (!$node->class instanceof Node\Name) {
            return false;
        }

        return $node->class->isFullyQualified() || $node->class->isQualified();
    }

    /**
     * @param Node&New_$node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var FullyQualified $class */
        $class = $node->class;

        return [$class->toString()];
    }
}
