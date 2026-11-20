<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

final class AutoloadType
{
    public const FILES = 'files';
    public const CLASSMAP = 'classmap';
    public const PSR0 = 'psr-0';
    public const PSR4 = 'psr-4';

    private function __construct()
    {
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::FILES,
            self::CLASSMAP,
            self::PSR0,
            self::PSR4
        ];
    }
}
