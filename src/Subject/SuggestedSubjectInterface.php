<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

interface SuggestedSubjectInterface
{
    public function addSuggestedBy(string $packageName): void;

    public function suggestsPackage(string $packageName): bool;

    /**
     * @return string[]
     */
    public function getSuggestedBy(): array;
}
