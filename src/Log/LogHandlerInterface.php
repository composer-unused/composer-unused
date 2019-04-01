<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

interface LogHandlerInterface
{
    public function handle(array $record): bool;
}
