<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use Closure;
use PhpParser\NodeVisitor;

interface SymbolCollectorInterface extends NodeVisitor
{
    /**
     * @return array<string>
     */
    public function getSymbolNames(): array;

    /**
     * Reset all previously found symbols
     */
    public function reset(): void;

    /**
     * Callback to add include to the iterated file lists
     */
    public function setFileIncludeCallback(Closure $includeCallback): void;
}
