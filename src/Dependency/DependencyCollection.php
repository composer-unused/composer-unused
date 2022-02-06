<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Dependency;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<DependencyInterface>
 */
final class DependencyCollection implements IteratorAggregate, Countable
{
    /** @var array<DependencyInterface> */
    private $items;

    /**
     * @param array<DependencyInterface> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return ArrayIterator<int, DependencyInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function add(DependencyInterface $dependency): void
    {
        $this->items[] = $dependency;
    }

    /**
     * @param Closure $partition
     * @return array<DependencyCollection>
     */
    public function partition(Closure $partition): array
    {
        $matches = [];
        $noMatches = [];

        foreach ($this->items as $item) {
            if ($partition($item)) {
                $matches[] = $item;
            } else {
                $noMatches[] = $item;
            }
        }

        return [
            new self($matches),
            new self($noMatches)
        ];
    }

    /**
     * @param Closure $fn
     * @return DependencyCollection
     */
    public function filter(Closure $fn): DependencyCollection
    {
        return new self(
            array_filter($this->items, $fn)
        );
    }

    public function merge(DependencyCollection $other): DependencyCollection
    {
        return new self(array_merge($this->items, $other->items));
    }

    public function map(Closure $callback): DependencyCollection
    {
        array_map($callback, $this->items);
        return $this;
    }
}
