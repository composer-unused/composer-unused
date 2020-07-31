<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\File;

use Icanhazstring\Composer\Unused\Exception\IOException;
use SplFileInfo;

class FileContentProvider
{
    /**
     * @throws IOException
     */
    public function getContent(?string $baseDir, SplFileInfo $file): string
    {
        $contents = file_get_contents($baseDir . $file->getPathname());

        if ($contents === false) {
            throw IOException::unableToOpenHandle($baseDir . $file->getPathname());
        }

        return $contents;
    }
}
