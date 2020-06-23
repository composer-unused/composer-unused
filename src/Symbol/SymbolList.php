<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Generator;
use Traversable;

final class SymbolList implements SymbolListInterface
{
    /** @var Traversable<SymbolInterface> */
    private $items;

    public function addAll(Traversable $symbols): SymbolListInterface
    {
        $this->items = iterator_to_array($symbols);
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
