<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

class Result implements ResultInterface
{
    private $items = [];
    private $skipped = [];

    public function addItem($item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function skipItem(string $item, string $reason)
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
