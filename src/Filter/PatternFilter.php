<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Configuration;

final class PatternFilter implements FilterInterface
{
    private Configuration\PatternFilter $pattern;
    private bool $used = false;
    private bool $alwaysUsed;

    public function __construct(Configuration\PatternFilter $pattern, bool $alwaysUsed = false)
    {
        $this->pattern = $pattern;
        $this->alwaysUsed = $alwaysUsed;
    }

    public static function fromString(string $pattern, bool $alwaysUsed = false): self
    {
        return new self(Configuration\PatternFilter::fromString($pattern), $alwaysUsed);
    }

    public function applies(DependencyInterface $dependency): bool
    {
        $applies = preg_match($this->pattern->toString(), $dependency->getName()) > 0;

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
            $this->pattern->toString()
        );
    }
}
