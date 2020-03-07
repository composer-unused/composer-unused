<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Icanhazstring\Composer\Unused\Subject\SubjectInterface;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;

class Result implements ResultInterface
{
    /** @var array<UsageInterface|SubjectInterface|object> */
    private $items = [];
    /** @var array<array<string, string>> */
    private $skipped = [];

    public function addItem(object $item): ResultInterface
    {
        $this->items[] = $item;

        return $this;
    }

    public function skipItem(string $item, string $reason): ResultInterface
    {
        $this->skipped[] = [
            'item'   => $item,
            'reason' => $reason
        ];

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function hasItems(): bool
    {
        return !empty($this->items);
    }

    public function getSkippedItems(): array
    {
        return $this->skipped;
    }

    public function hasSkippedItems(): bool
    {
        return !empty($this->skipped);
    }
}
