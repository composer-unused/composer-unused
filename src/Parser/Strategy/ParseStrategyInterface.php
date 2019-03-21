<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;

interface ParseStrategyInterface
{
    public function meetsCriteria(Node $node): bool;

    public function extractNamespace(Node $node): string;
}
