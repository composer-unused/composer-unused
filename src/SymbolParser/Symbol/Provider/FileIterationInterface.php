<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Provider;

use Iterator;
use SplFileInfo;

interface FileIterationInterface
{
    /**
     * @param Iterator<string|int, SplFileInfo> $files
     */
    public function appendFiles(Iterator $files): void;
}
