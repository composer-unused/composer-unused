<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

use SplFileInfo;

interface UsageInterface
{
    public function getFile(): SplFileInfo;

    public function getNamespace(): string;

    public function getLine(): int;
}
