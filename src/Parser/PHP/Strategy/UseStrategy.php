<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

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
    public function extractSymbols(Node $node): array
    {
        if ($node instanceof Use_) {
            return [$node->uses[0]->name->toString()];
        }

        if ($node instanceof GroupUse) {
            $prefix = $node->prefix->toString();

            return array_map(static function (UseUse $use) use ($prefix) {
                return $prefix . '\\' . $use->name->toString();
            }, $node->uses);
        }

        return [];
    }
}
