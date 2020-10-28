<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;

interface ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool;

    /**
     * @param Node $node
     * @return string[]
     * @phpstan-return array<class-string>
     */
    public function extractNamespaces(Node $node): array;
}
