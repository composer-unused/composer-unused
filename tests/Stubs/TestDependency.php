<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Stubs;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;

final class TestDependency implements DependencyInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function inState(string $state): bool
    {
        return false;
    }

    public function provides(SymbolInterface $symbol): bool
    {
        return false;
    }

    public function requires(DependencyInterface $dependency): bool
    {
        return false;
    }

    public function suggests(DependencyInterface $dependency): bool
    {
        return false;
    }

    public function requiredBy(DependencyInterface $dependency): void
    {
    }

    public function getRequiredBy(): array
    {
        return [];
    }

    public function suggestedBy(DependencyInterface $dependency): void
    {
    }

    public function getSuggestedBy(): array
    {
        return [];
    }
}
