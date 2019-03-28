<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;

class NullConstraintFilter implements FilterInterface
{
    public function match(Link $item): bool
    {
        return $item->getConstraint() === null;
    }

    public function getReason(): string
    {
        return 'Invalid constraint';
    }
}
