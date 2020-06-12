<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\Symbol;

interface DependencyInterface
{
    public function provides(Symbol $symbol): bool;
}
