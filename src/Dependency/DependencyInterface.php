<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

interface DependencyInterface
{
    public const STATE_INVALID = 'invalid';
    public const STATE_USED = 'used';
    public const STATE_UNUSED = 'unused';

    public function getName(): string;

    public function inState(string $state): bool;

    public function provides(SymbolInterface $symbol): bool;

    public function requires(DependencyInterface $dependency): bool;
}
