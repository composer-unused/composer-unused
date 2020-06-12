<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use ArrayIterator;
use IteratorAggregate;

final class SymbolList implements IteratorAggregate, SymbolListInterface
{
    /** @var array<Symbol> */
    private $items;

    public function addAll(array $symbols): SymbolListInterface
    {
        $clone = $this;

        foreach ($symbols as $symbol) {
            $clone = $clone->add($symbol);
        }

        return $clone;
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

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
