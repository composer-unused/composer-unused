<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;

interface ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool;

    /**
     * @param Node $node
     * @return string[]
     */
    public function extractNamespaces(Node $node): array;
}
