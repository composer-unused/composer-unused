<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;

interface FilterInterface
{
    public function match(Link $item): bool;

    public function getReason(): string;
}
