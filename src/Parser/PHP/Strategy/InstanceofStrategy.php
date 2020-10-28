<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;

final class InstanceofStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
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
     * @phpstan-return array<class-string>
     */
    public function extractNamespaces(Node $node): array
    {
        /** @var Node\Name $class */
        $class = $node->class;

        return [
            $class->toString(),
        ];
    }
}
