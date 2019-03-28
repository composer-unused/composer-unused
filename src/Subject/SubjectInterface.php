<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

interface SubjectInterface
{
    public function providesNamespace(string $usedNamespace): bool;

    public function getName(): string;

    public function addSuggestedBy(string $packageName): void;

    public function suggestsPackage(string $packageName): bool;

    /**
     * @return string[]
     */
    public function getSuggestedBy(): array;
}
