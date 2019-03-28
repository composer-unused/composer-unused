<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;

class ExcludePackageFilter implements FilterInterface
{
    /** @var array */
    private $excludes;

    /**
     * @param string[] $excludes
     */
    public function __construct(array $excludes)
    {
        $this->excludes = $excludes;
    }

    public function match(Link $item): bool
    {
        return in_array($item->getTarget(), $this->excludes, true);
    }

    public function getReason(): string
    {
        return 'Package excluded by cli/config';
    }
}
