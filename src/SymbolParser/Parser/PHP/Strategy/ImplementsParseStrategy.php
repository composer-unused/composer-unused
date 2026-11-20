<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

final class ImplementsParseStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof Class_) {
            return false;
        }

        if (empty($node->implements)) {
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
        /** @var Node\Name[] $implements */
        $implements = $node->implements;

        $namespaces = [];

        foreach ($implements as $implement) {
            $namespaces[] = $implement->toString();
        }

        return $namespaces;
    }
}
