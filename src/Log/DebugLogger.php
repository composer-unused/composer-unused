<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log;

use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Throwable;

class DebugLogger implements LoggerInterface
{
    use LoggerTrait;

    /** @var LogHandlerInterface */
    private $handler;

    public function __construct(LogHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array<string, mixed> $context
     */
    public function log($level, $message, array $context = []): void
    {
        try {
            $this->handler->handle($this->buildRecord($level, $message, $context));
        } catch (Exception $e) {
            // Bad luck :D
        }
    }

    /**
     * @param mixed $level
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     * @throws Exception
     */
    private function buildRecord($level, string $message, array $context = []): array
    {
        if (array_key_exists('error', $context)) {
            /** @var Throwable $error */
            $error = $context['error'];
            unset($context['error']);

            $context['file'] = $error->getFile();
            $context['line'] = $error->getLine();
        }

        return [
            'time'    => (new DateTimeImmutable())->format('Y-m-d\TH:i:s.uP'),
            'level'   => $level,
            'message' => $message,
            'context' => $context
        ];
    }
}
