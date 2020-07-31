<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;

interface StrategyInterface
{
    public function canHandle(Node $node): bool;

    /**
     * @param Node $node
     * @return array<string>
     */
    public function extractSymbols(Node $node): array;
}
