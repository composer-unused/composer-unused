<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;

final class PatternFilter implements FilterInterface
{
    private string $pattern;
    private bool $used = false;
    private bool $alwaysUsed;

    public function __construct(string $pattern, bool $alwaysUsed = false)
    {
        $this->pattern = $pattern;
        $this->alwaysUsed = $alwaysUsed;
    }

    public function applies(DependencyInterface $dependency): bool
    {
        return $this->used = (bool)preg_match($this->pattern, $dependency->getName());
    }

    public function used(): bool
    {
        return $this->alwaysUsed || $this->used;
    }

    public function toString(): string
    {
        return $this->pattern;
    }
}
