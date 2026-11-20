<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol;

use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<SymbolInterface>
 */
interface SymbolListInterface extends IteratorAggregate
{
    public function add(SymbolInterface $symbol): self;

    /**
     * @param Traversable<SymbolInterface> $symbols
     */
    public function addAll(Traversable $symbols): self;

    public function contains(SymbolInterface $symbol): bool;
}
