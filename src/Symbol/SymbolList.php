<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Generator;
use IteratorAggregate;

final class SymbolList implements IteratorAggregate, SymbolListInterface
{
    /** @var iterable<SymbolInterface> */
    private $items = [];

    public function addAll(iterable $symbols): SymbolListInterface
    {
        $this->items = $symbols;
        return $this;
    }

    public function add(Symbol $symbol): SymbolListInterface
    {
        $clone = clone $this;
        $clone->items[] = $symbol;

        return $clone;
    }

    public function contains(Symbol $symbol): bool
    {
        foreach ($this->items as $item) {
            if ($item->matches($symbol)) {
                return true;
            }
        }

        return false;
    }

    public function getIterator(): Generator
    {
        yield from $this->items;
    }
}
