<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

interface RequiredSubjectInterface
{
    public function addRequiredBy(string $packageName): void;

    public function requiresPackage(string $packageName): bool;

    /**
     * @return string[]
     */
    public function getRequiredBy(): array;
}
