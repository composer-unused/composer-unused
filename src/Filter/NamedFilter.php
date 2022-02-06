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
        $applies = $dependency->getName() === $this->filterString;

        if ($this->used === false && $applies === true) {
            $this->used = true;
        }

        return $applies;
    }

    public function used(): bool
    {
        return $this->alwaysUsed || $this->used;
    }

    public function toString(): string
    {
        $type = substr(self::class, strrpos(self::class, '\\') + 1);

        return sprintf(
            '%s(userProvided: %s, string: %s)',
            $type,
            $this->alwaysUsed ? 'false' : 'true',
            $this->filterString
        );
    }
}
