<?php // phpcs:disable

declare(strict_types=1);

class StaticVariableCall
{
    public static function bar(): void
    {
    }
}

$fu = new StaticVariableCall();
$fu::bar();
