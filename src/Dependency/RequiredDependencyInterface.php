<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

interface RequiredDependencyInterface
{
    public function markUsed(): void;

    public function isUsed(): bool;

    public function provides(SymbolInterface $symbol): bool;
}
