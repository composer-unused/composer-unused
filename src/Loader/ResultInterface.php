<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Icanhazstring\Composer\Unused\Subject\SubjectInterface;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;

interface ResultInterface
{
    /**
     * @var UsageInterface|SubjectInterface $item
     * @return static
     */
    public function addItem($item);

    /**
     * @param string $item
     * @param string $reason
     * @return static
     */
    public function skipItem(string $item, string $reason);

    public function getItems(): array;

    public function hasItems(): bool;

    public function getSkippedItems(): array;

    public function hasSkippedItems(): bool;
}
