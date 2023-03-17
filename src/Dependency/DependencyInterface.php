<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Dependency;

use ComposerUnused\SymbolParser\Symbol\SymbolInterface;

interface DependencyInterface
{
    public const STATE_INVALID = 'invalid';
    public const STATE_IGNORED = 'ignored';
    public const STATE_USED = 'used';
    public const STATE_UNUSED = 'unused';

    public function getName(): string;

    public function getUrl(): ?string;

    public function inState(string $state): bool;

    public function provides(SymbolInterface $symbol): bool;

    public function requires(DependencyInterface $dependency): bool;

    public function suggests(DependencyInterface $dependency): bool;

    public function requiredBy(DependencyInterface $dependency): void;

    /**
     * @return array<DependencyInterface>
     */
    public function getRequiredBy(): array;

    public function suggestedBy(DependencyInterface $dependency): void;

    /**
     * @return array<DependencyInterface>
     */
    public function getSuggestedBy(): array;

    public function getStateReason(): string;

    public function markUsed(): void;

    public function markIgnored(string $reason): void;
}
