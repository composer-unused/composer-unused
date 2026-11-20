<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP;

use ComposerUnused\SymbolParser\Parser\PHP\FileInclude;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

final class FileIncludeTest extends TestCase
{
    /**
     * @test
     */
    public function shouldAvoidNonStaticPaths(): void
    {
        $code = <<<'CODE'
        <?php

        include __DIR__ . DIRECTORY_SEPARATOR . sprintf(
            '%s.php',
            ltrim(str_replace('\\', '/', 'Foo\\Test\\Class'), 'Foo/')
        );
        CODE;

        $factory = new ParserFactory();
        $parser = $factory->createForNewestSupportedVersion();

        $nodes = $parser->parse($code);
        // @phpstan-ignore-next-line
        $concat = $nodes[0]->expr->expr;

        $fileInclude = FileInclude::fromConcatOperation($concat);
        self::assertSame($fileInclude->getPath(), '');
    }

    /**
     * @test
     */
    public function itParsesStringLiteralsFromConcat(): void
    {
        $code = <<<'CODE'
        <?php

        include __DIR__ . 'awesome/file/path.php';
        CODE;

        $factory = new ParserFactory();
        $parser = $factory->createForNewestSupportedVersion();

        $nodes = $parser->parse($code);
        // @phpstan-ignore-next-line
        $concat = $nodes[0]->expr->expr;

        $fileInclude = FileInclude::fromConcatOperation($concat);
        self::assertSame($fileInclude->getPath(), 'awesome/file/path.php');
    }

    /**µ
     * @test
     */
    public function itParsesScalarString(): void
    {
        $code = <<<'CODE'
        <?php

        include 'awesome/file/path.php';
        CODE;

        $factory = new ParserFactory();
        $parser = $factory->createForNewestSupportedVersion();

        $nodes = $parser->parse($code);
        // @phpstan-ignore-next-line
        $scalar = $nodes[0]->expr->expr;

        $fileInclude = FileInclude::fromScalar($scalar);
        self::assertSame($fileInclude->getPath(), 'awesome/file/path.php');
    }
}
