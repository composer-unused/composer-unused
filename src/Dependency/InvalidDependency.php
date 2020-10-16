<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Composer\Package\Link;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

final class InvalidDependency implements DependencyInterface
{
    /** @var Link */
    private $linkedPackage;
    /** @var string */
    private $reason;

    public function __construct(Link $linkedPackage, string $reason)
    {
        $this->linkedPackage = $linkedPackage;
        $this->reason = $reason;
    }

    public function getName(): string
    {
        return $this->linkedPackage->getTarget();
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function inState(string $state): bool
    {
        return self::STATE_INVALID === $state;
    }

    public function provides(SymbolInterface $symbol): bool
    {
        return false;
    }

    public function requires(DependencyInterface $dependency): bool
    {
        return false;
    }
}
