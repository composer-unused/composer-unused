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
    public function getContent(SplFileInfo $file): string
    {
        $contents = file_get_contents($file->getPathname());

        if ($contents === false) {
            throw IOException::unableToOpenHandle($file->getPathname());
        }

        return $contents;
    }
}
