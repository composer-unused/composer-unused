<?php // phpcs:disable

declare(strict_types=1);

class StaticNonFullyQualifiedCall
{
    public static function bar(): string
    {
        return '';
    }
}

$var = StaticNonFullyQualifiedCall::bar();
