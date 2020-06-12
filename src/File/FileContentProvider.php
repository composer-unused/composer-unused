<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\File;

class FileContentProvider
{
    public function getContent(?string $baseDir, string $file)
    {
        return file_get_contents($baseDir . $file);
    }
}
