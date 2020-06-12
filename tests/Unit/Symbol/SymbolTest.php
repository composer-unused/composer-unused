<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol;

use Icanhazstring\Composer\Unused\Symbol\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldMatchOtherSymbolWithSameName(): void
    {
        $symbol = new Symbol('test');

        self::assertTrue($symbol->matches(new Symbol('test')));
    }
}
