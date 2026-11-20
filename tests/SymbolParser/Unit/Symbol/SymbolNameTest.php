<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Symbol;

use ComposerUnused\SymbolParser\Symbol\SymbolName;
use Generator;
use PHPUnit\Framework\TestCase;

final class SymbolNameTest extends TestCase
{
    /**
     * @dataProvider isPartOfDataProvider
     * @test
     */
    public function itShouldResultAsPartOf(string $first, string $second, bool $expected): void
    {
        $symbolA = new SymbolName($first);
        $symbolB = new SymbolName($second);

        self::assertSame($expected, $symbolA->isPartOf($symbolB));
    }


    /**
     * @return Generator<string, array<string|bool>>
     */
    public static function isPartOfDataProvider(): Generator
    {
        yield 'A not part of B' => [
            'A', 'B', false
        ];

        yield 'A is part of A' => [
            'A', 'A', true
        ];

        yield 'B is part of A\B' => [
            'B', 'A\B', true
        ];

        yield 'B\C is part of A\B' => [
            'B\C' , 'A\B', true
        ];

        yield 'A\B\C is part of C\B\A' => [
            'A\B\C' , 'C\B\A', true
        ];

        yield 'nothing not part of A\B' => [
            '', 'A\B', false
        ];

        yield 'A\B not part of nothing' => [
            'A\B', '', false
        ];

        yield 'array_keys part of \array_keys' => [
            'array_keys', '\array_keys', true
        ];

        yield 'B part of \A\B' => [
            'B', '\A\B', true
        ];
    }

    /**
     * @dataProvider itMergesSymbolNameDataProvider
     * @test
     */
    public function itMergesSymbolNames(string $first, string $second, string $expected): void
    {
        $symbolA = new SymbolName($first);
        $symbolB = new SymbolName($second);

        self::assertSame($expected, $symbolA->merge($symbolB)->getName());
    }

    /**
     * @return Generator<string, array<string>>
     */
    public static function itMergesSymbolNameDataProvider(): Generator
    {
        yield 'A\B with B equals A\B' => [
            'A\B', 'B', 'A\B'
        ];

        yield 'A\B with B\C equals A\B\C' => [
            'A\B', 'B\C',  'A\B\C'
        ];

        yield 'empty with A\B\C equals A\B\C' => [
            '', 'A\B\C', 'A\B\C'
        ];

        yield 'array_keys with \array_keys equals array_keys' => [
            'array_keys', '\array_keys', 'array_keys'
        ];

        yield 'B\NS\MyClass with MyClass equals B\NS\MyClass' => [
            'B\NS\MyClass', 'MyClass', 'B\NS\MyClass'
        ];
    }
}
