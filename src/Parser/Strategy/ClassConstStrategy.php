<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;

class ClassConstStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
    {
        if (!$node instanceof ClassConstFetch) {
            return false;
        }

        if (!$node->class instanceof Node\Name) {
            return false;
        }

        return $node->class->isFullyQualified() || $node->class->isQualified();
    }

    /**
     * @param Node&ClassConstFetch $node
     * @return array
     */
    public function extractNamespaces(Node $node): array
    {
        /** @var Node\Name $class */
        $class = $node->class;

        return [$class->toString()];
    }
}
