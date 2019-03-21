<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;

class UseParseStrategy implements ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool
    {
        return $node instanceof Use_;
    }

    /**
     * @param Node&Use_ $node
     * @return string
     */
    public function extractNamespace(Node $node): string
    {
        return $node->uses[0]->name->toString();
    }
}
