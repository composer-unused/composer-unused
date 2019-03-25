<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class DebugLogger implements LoggerInterface
{
    use LoggerTrait;

    private $logs = [];

    public function log($level, $message, array $context = []): void
    {
        $this->logs[$level] = $this->logs[$level] ?? [];
        $this->logs[$level][] = $message;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}
