<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Icanhazstring\Composer\Unused\Subject\SubjectInterface;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;

interface ResultInterface
{
    /**
     * @var object|UsageInterface|SubjectInterface $item
     */
    public function addItem(object $item): self;

    public function skipItem(string $item, string $reason): self;

    /**
     * @return array<mixed>
     */
    public function getItems(): array;

    public function hasItems(): bool;

    /**
     * @return array<array<string, string>>
     */
    public function getSkippedItems(): array;

    public function hasSkippedItems(): bool;
}
