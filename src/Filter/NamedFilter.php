<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Configuration;

final class NamedFilter implements FilterInterface
{
    private Configuration\NamedFilter $filter;
    private bool $used = false;
    private bool $alwaysUsed;

    public function __construct(Configuration\NamedFilter $filterString, bool $alwaysUsed = false)
    {
        $this->filter = $filterString;
        $this->alwaysUsed = $alwaysUsed;
    }

    public static function fromString(string $filterString, bool $alwaysUsed = false): self
    {
        return new self(Configuration\NamedFilter::fromString($filterString), $alwaysUsed);
    }

    public function applies(DependencyInterface $dependency): bool
    {
        $applies = $dependency->getName() === $this->filter->toString();

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
            $this->filter->toString()
        );
    }
}
