<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration;

final class PatternFilter implements FilterInterface
{
    private string $string;

    private function __construct(string $string)
    {
        if (empty($string)) {
            throw new \InvalidArgumentException('PatternFilter value must not be empty');
        }
        $this->string = $string;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function toString(): string
    {
        return $this->string;
    }
}
