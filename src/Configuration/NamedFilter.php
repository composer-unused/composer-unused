<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration;

use Webmozart\Assert\Assert;

final class NamedFilter implements FilterInterface
{
    private string $string;

    private function __construct(string $string)
    {
        Assert::notEmpty($string, 'NamedFilter value must not be empty');
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
