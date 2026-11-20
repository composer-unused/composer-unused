<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;

use function sprintf;

final class ClassConstStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof ClassConstFetch) {
            return false;
        }

        if (!$node->class instanceof Node\Name) {
            return false;
        }

        return true;
    }

    /**
     * @param Node&ClassConstFetch $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var Node\Name $class */
        $class = $node->class;

        return [$class->name];
    }
}
