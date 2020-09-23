<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Generator;
use Traversable;

use function iterator_to_array;

final class SymbolList implements SymbolListInterface
{
    /** @var array<SymbolInterface> */
    private $items;

    /**
     * @param Traversable<SymbolInterface> $symbols
     */
    public function addAll(Traversable $symbols): SymbolListInterface
    {
        $this->items = iterator_to_array($symbols);
        return $this;
    }

    public function add(SymbolInterface $symbol): SymbolListInterface
    {
        $clone = clone $this;
        $clone->items[] = $symbol;

        return $clone;
    }

    public function contains(SymbolInterface $symbol): bool
    {
        foreach ($this->items as $item) {
            if ($item->matches($symbol)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function getIterator(): Generator
    {
        yield from $this->items;
    }
}
