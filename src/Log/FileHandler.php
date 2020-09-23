<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

use Icanhazstring\Composer\Unused\Exception\IOException;

class FileHandler implements LogHandlerInterface
{
    /** @var resource */
    private static $fileHandle;
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(array $record): bool
    {
        if (self::$fileHandle === null) {
            $handle = fopen($this->path, 'ab');

            if ($handle === false) {
                throw IOException::unableToOpenHandle($this->path);
            }

            self::$fileHandle = $handle;
        }

        return fwrite(self::$fileHandle, json_encode($record, JSON_THROW_ON_ERROR) . PHP_EOL) !== false;
    }
}
