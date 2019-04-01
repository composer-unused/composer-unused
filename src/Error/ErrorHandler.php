<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error;

use PhpParser\Error;
use Psr\Log\LoggerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adapter for php-parser error handler.
     *
     * @param Error $error
     * @return void
     * @throws Throwable
     */
    public function handleError(Error $error): void
    {
        $this->handle($error);
    }

    public function handle(Throwable $error): void
    {
        $this->logger->error($error->getMessage(), ['error' => $error]);
    }
}
