<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Expression;

class StaticParseStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
    {
        if (!($node instanceof Expression && $node->expr instanceof StaticCall)) {
            return false;
        }

        /** @var StaticCall $expression */
        $expression = $node->expr;
        /** @var FullyQualified $class */
        $class = $expression->class;

        return $class->isFullyQualified();
    }

    /**
     * @param Node&Expression $node
     * @return string
     */
    public function extractNamespace(Node $node): string
    {
        /** @var StaticCall $expression */
        $expression = $node->expr;
        /** @var FullyQualified $class */
        $class = $expression->class;

        return $class->toString();
    }
}
