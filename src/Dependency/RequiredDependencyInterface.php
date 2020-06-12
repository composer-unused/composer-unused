<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\Symbol;

interface RequiredDependencyInterface
{
    public function markUsed(): void;
    public function isUsed(): bool;
    public function provides(Symbol $symbol): bool;
}
