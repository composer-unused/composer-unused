<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol;

final class Symbol implements SymbolInterface
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function matches(SymbolInterface $symbol): bool
    {
        return $this->identifier === $symbol->getIdentifier();
    }
}
