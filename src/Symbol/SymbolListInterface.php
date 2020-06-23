<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use IteratorAggregate;
use Traversable;

interface SymbolListInterface extends IteratorAggregate
{
    public function add(Symbol $symbol): self;

    public function addAll(Traversable $symbols): self;

    public function contains(Symbol $symbol): bool;
}
