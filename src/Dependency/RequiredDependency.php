<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\Symbol;

final class RequiredDependency implements RequiredDependencyInterface
{
    /** @var bool */
    private $used = false;
    /** @var DependencyInterface */
    private $dependency;

    public function __construct(DependencyInterface $dependency)
    {
        $this->dependency = $dependency;
    }

    public function markUsed(): void
    {
        $this->used = true;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function provides(Symbol $symbol): bool
    {
        return $this->dependency->provides($symbol);
    }
}
