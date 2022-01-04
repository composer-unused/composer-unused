<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use ComposerUnused\SymbolParser\Symbol\SymbolListInterface;

use function array_key_exists;

final class RequiredDependency implements DependencyInterface
{
    /** @var string */
    private $state = self::STATE_UNUSED;
    /** @var PackageInterface */
    private $package;
    /** @var SymbolListInterface */
    private $symbols;
    /** @var array<DependencyInterface> */
    private $requiredBy = [];
    /** @var array<DependencyInterface> */
    private $suggestBy = [];

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
        $this->state = self::STATE_USED;
    }

    public function inState(string $state): bool
    {
        return $this->state === $state;
    }

    public function provides(SymbolInterface $symbol): bool
    {
        return $this->symbols->contains($symbol);
    }

    public function requires(DependencyInterface $dependency): bool
    {
        foreach ($this->package->getRequires() as $require) {
            if ($require->getTarget() === $dependency->getName()) {
                return true;
            }
        }

        return false;
    }

    public function suggests(DependencyInterface $dependency): bool
    {
        return in_array($dependency->getName(), $this->package->getSuggests(), true);
    }

    public function requiredBy(DependencyInterface $dependency): void
    {
        $this->requiredBy[$dependency->getName()] = $dependency;
    }

    public function getRequiredBy(): array
    {
        return $this->requiredBy;
    }

    public function suggestedBy(DependencyInterface $dependency): void
    {
        $this->suggestBy[$dependency->getName()] = $dependency;
    }

    public function getSuggestedBy(): array
    {
        return $this->suggestBy;
    }
}
