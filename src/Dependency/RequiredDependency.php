<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolListInterface;

final class RequiredDependency implements RequiredDependencyInterface
{
    /** @var bool */
    private $used = false;
    /** @var PackageInterface */
    private $package;
    /** @var SymbolListInterface */
    private $symbols;

    public function __construct(PackageInterface $package, SymbolListInterface $symbols)
    {
        $this->package = $package;
        $this->symbols = $symbols;
    }

    public function getName(): string
    {
        return $this->package->getName();
    }

    public function markUsed(): void
    {
        $this->used = true;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function provides(SymbolInterface $symbol): bool
    {
        return $this->symbols->contains($symbol);
    }
}
