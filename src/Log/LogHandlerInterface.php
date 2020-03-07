<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

interface LogHandlerInterface
{
    /**
     * @param array<string, mixed> $record
     */
    public function handle(array $record): bool;
}
