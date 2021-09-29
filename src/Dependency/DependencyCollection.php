<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

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

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function count()
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
}
