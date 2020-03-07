<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;

class NewParseStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
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
    public function extractNamespaces(Node $node): array
    {
        /** @var FullyQualified $class */
        $class = $node->class;

        return [$class->toString()];
    }
}
