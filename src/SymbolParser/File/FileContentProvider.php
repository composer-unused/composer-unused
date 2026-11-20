<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\File;

use ComposerUnused\SymbolParser\Exception\IOException;
use SplFileInfo;

class FileContentProvider
{
    /**
     * @throws IOException
     */
    public function getContent(SplFileInfo $file): string
    {
        if (!file_exists($file->getPathname())) {
            throw IOException::fileDoesNotExist($file->getPathname());
        }

        $contents = file_get_contents($file->getPathname());

        if ($contents === false) {
            throw IOException::unableToOpenHandle($file->getPathname());
        }

        return $contents;
    }
}
