<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol;

final class NamespaceSymbol implements SymbolInterface
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public static function fromClass(string $class): self
    {
        return new self(implode('\\', explode('\\', $class, -1)));
    }

    public function getIdentifier(): string
    {
        return $this->namespace;
    }

    public function matches(SymbolInterface $symbol): bool
    {
        return strpos($symbol->getIdentifier(), $this->namespace) === 0;
    }
}
