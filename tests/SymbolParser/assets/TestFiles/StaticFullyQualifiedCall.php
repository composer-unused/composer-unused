<?php // phpcs:disable

declare(strict_types=1);

class StaticFullyQualifiedCall
{
    public static function bar(): string
    {
        return '';
    }
}

$var = \StaticFullyQualifiedCall::bar();
