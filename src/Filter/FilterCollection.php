<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ArrayIterator;
use ComposerUnused\ComposerUnused\Configuration;
use Countable;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<FilterInterface>
 */
final class FilterCollection implements IteratorAggregate, Countable
{
    /**
     * List of global named packages which should be excluded from unused check
     * Key => package name
     * Value => always "used"
     */
    private const GLOBAL_NAMED_EXCLUSION = [
        'composer' => true,
        'composer-plugin-api' => true,
        'composer-runtime-api' => true,
        'composer-unused/composer-unused-plugin' => true
    ];

    /**
     * List of global pattern which should be excluded from unused check
     * Key => pattern
     * Value => always "used"
     */
    private const GLOBAL_PATTERN_EXCLUSION = [
        '/-implementation$/i' => true
    ];

    /** @var array<FilterInterface> */
    private array $items;

    /**
     * @param array<Configuration\NamedFilter> $namedFilter
     * @param array<Configuration\PatternFilter> $patternFilter
     */
    public function __construct(array $namedFilter, array $patternFilter)
    {
        $globalNamedFilter = array_map(
            static fn(
                string $named,
                bool $used
            ) => new NamedFilter(Configuration\NamedFilter::fromString($named), $used),
            array_keys(self::GLOBAL_NAMED_EXCLUSION),
            array_values(self::GLOBAL_NAMED_EXCLUSION)
        );

        $globalPatternFilter =
            array_map(
                static fn(
                    string $pattern,
                    bool $used
                ) => new PatternFilter(Configuration\PatternFilter::fromString($pattern), $used),
                array_keys(self::GLOBAL_PATTERN_EXCLUSION),
                array_values(self::GLOBAL_PATTERN_EXCLUSION)
            );

        $named = array_map(static fn(Configuration\NamedFilter $named) => new NamedFilter($named), $namedFilter);
        $pattern = array_map(
            static fn(Configuration\PatternFilter $pattern) => new PatternFilter($pattern),
            $patternFilter
        );

        $this->items = array_merge($globalNamedFilter, $globalPatternFilter, $named, $pattern);
    }

    /**
     * @return ArrayIterator<int, FilterInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array<FilterInterface>
     */
    public function getUnused(): array
    {
        return array_filter(
            $this->items,
            static fn(FilterInterface $filter) => $filter->used() === false
        );
    }
}
