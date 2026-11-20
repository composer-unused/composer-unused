<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;

final class TypedAttributeStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof Node\Stmt\Property) {
            return false;
        }

        return $node->type instanceof FullyQualified;
    }

    /**
     * @param Node&Node\Stmt\Property $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var FullyQualified $type */
        $type = $node->type;

        return [$type->toString()];
    }
}
