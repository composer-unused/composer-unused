<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use function array_merge;

class ImplementsParseStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
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
    public function extractNamespaces(Node $node): array
    {
        /** @var Node\Name[] $class */
        $implements = $node->implements;

        $namespaces = [];

        foreach ($implements as $implement) {
            $namespaces[] = $implement->toString();
        }

        return $namespaces;
    }
}
