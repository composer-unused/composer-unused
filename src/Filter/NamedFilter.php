<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;

final class NamedFilter implements FilterInterface
{
    private string $filterString;
    private bool $used = false;
    private bool $alwaysUsed;

    public function __construct(string $filterString, bool $alwaysUsed = false)
    {
        $this->filterString = $filterString;
        $this->alwaysUsed = $alwaysUsed;
    }

    public function applies(DependencyInterface $dependency): bool
    {
        return $this->used = $dependency->getName() === $this->filterString;
    }

    public function used(): bool
    {
        return $this->alwaysUsed || $this->used;
    }

    public function toString(): string
    {
        return $this->filterString;
    }
}
