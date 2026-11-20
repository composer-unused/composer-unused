<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;

class UseStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        return $node instanceof Use_ || $node instanceof GroupUse;
    }

    /**
     * @param Node $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        if ($node instanceof Use_) {
            return [$node->uses[0]->name->toString()];
        }

        if ($node instanceof GroupUse) {
            $prefix = $node->prefix->toString();

            return array_map(static function ($use) use ($prefix) {
                return $prefix . '\\' . $use->name->toString();
            }, $node->uses);
        }

        return [];
    }
}
