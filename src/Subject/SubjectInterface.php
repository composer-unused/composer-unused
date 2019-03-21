<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

interface SubjectInterface
{
    public function providesNamespace(string $namespace): bool;

    public function getName(): string;
}
