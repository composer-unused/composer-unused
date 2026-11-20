<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol;

interface SymbolInterface
{
    public function getIdentifier(): string;
    public function matches(SymbolInterface $symbol): bool;
}
