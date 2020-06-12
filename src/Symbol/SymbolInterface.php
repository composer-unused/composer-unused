<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

interface SymbolInterface
{
    public function getIdentifier(): string;
    public function matches(SymbolInterface $symbol): bool;
}
