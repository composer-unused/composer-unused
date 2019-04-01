<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

class FileHandler implements LogHandlerInterface
{
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
            self::$fileHandle = fopen($this->path, 'ab');
        }

        return fwrite(self::$fileHandle, json_encode($record) . PHP_EOL) !== false;
    }
}
