<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

interface RequiredDependencyInterface extends DependencyInterface
{
    public function markUsed(): void;
    public function isUsed(): bool;
}
