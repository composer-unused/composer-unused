<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use PhpParser\NodeVisitor;

interface SymbolCollectorInterface extends NodeVisitor
{
    /**
     * @return array<string>
     */
    public function getSymbolNames(): array;
}
