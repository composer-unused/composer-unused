<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use Composer\InstalledVersions;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\AnnotationStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\ParserConfig;

final class AnnotationStrategyTest extends ParserTestCase
{
    private AnnotationStrategy $strategy;

    protected function setUp(): void
    {
        $phpStanPhpDocParserVersion = InstalledVersions::getVersion('phpstan/phpdoc-parser');
        if (!is_string($phpStanPhpDocParserVersion)) {
            throw new \RuntimeException('Unable to get phpstan/phpdoc-parser version');
        }
        $phpStanPhpDocParserV2 = substr($phpStanPhpDocParserVersion, 0, 1) === '2';
        $arguments = [];
        if ($phpStanPhpDocParserV2) {
            $parserConfig = new ParserConfig(['lines' => true, 'indexes' => true, 'comments' => true]);
            $arguments[] = new ConstExprParser($parserConfig);
            $arguments[] = new Lexer($parserConfig);
            $arguments[] = $parserConfig;
        } else {
            // @phpstan-ignore-next-line
            $arguments[] = new ConstExprParser();
            // @phpstan-ignore-next-line
            $arguments[] = new Lexer();
        }
        $this->strategy = new AnnotationStrategy(
            ...$arguments,
        );
    }

    /**
     * @test
     */
    public function itShouldParseAnnotations(): void
    {
        $code = <<<CODE
        <?php

        namespace Test;

        /**
         * @My\Namespace\Lorem(x=100, y="foo")
         * @mixin My\Namespace\Ipsum
         * @property My\Namespace\Dolor \$foo
         * @property-read My\Namespace\Sit \$foo
         * @property-write My\Namespace\Amet \$foo
         * @method My\Namespace\Consetetur lorem(My\Namespace\Sadipscing \$a, My\Namespace\Elitr \$b)
         */
        final class MyClass
        {
            /** @My\Namespace\Sed */
            private int \$a;

            /** @var My\Namespace\Diam */
            private \$b;

            /** @My\Namespace\Nonumy */
            public function ipsum(): void {}

            /** @param My\Namespace\Eirmod \$z */
            public function dolor(\$c): void {}

            /** @return My\Namespace\Tempor|My\Namespace\Invidunt */
            public function sit() {}

            /** @return My\Namespace\Ut&My\Namespace\Labore */
            public function amet() {}

            /** @return My\Namespace\Et<My\Namespace\Dolore > */
            public function consetetur() {}

            /** @return My\Namespace\Magna[] */
            public function sadipscing() {}

            /** @return array{'foo': My\Namespace\Aliquyam} */
            public function elitr() {}

            /** @return callable(My\Namespace\Erat): My\Namespace\Voluptua */
            public function sed() {}

            /** @return (\$x is My\Namespace\At ? My\Namespace\Vero : My\Namespace\Eos) */
            public function diam(\$x) {}

            /** @return ?My\Namespace\Accusam */
            public function nonumy() {}

            /** @return My\Namespace\Justo[My\Namespace\Dolores] */
            public function eirmod() {}
        }
        CODE;

        self::assertSame(
            [
                'My\Namespace\Lorem',
                'My\Namespace\Ipsum',
                'My\Namespace\Dolor',
                'My\Namespace\Sit',
                'My\Namespace\Amet',
                'My\Namespace\Consetetur',
                'My\Namespace\Sadipscing',
                'My\Namespace\Elitr',
                'My\Namespace\Sed',
                'My\Namespace\Diam',
                'My\Namespace\Nonumy',
                'My\Namespace\Eirmod',
                'My\Namespace\Tempor',
                'My\Namespace\Invidunt',
                'My\Namespace\Ut',
                'My\Namespace\Labore',
                'My\Namespace\Et',
                'My\Namespace\Dolore',
                'My\Namespace\Magna',
                'My\Namespace\Aliquyam',
                'My\Namespace\Voluptua',
                'My\Namespace\Erat',
                'My\Namespace\At',
                'My\Namespace\Vero',
                'My\Namespace\Eos',
                'My\Namespace\Accusam',
                'My\Namespace\Justo',
                'My\Namespace\Dolores',
            ],
            $this->parseConsumedSymbols([$this->strategy], $code)
        );
    }

    /**
     * @test
     * @see https://github.com/composer-unused/symbol-parser/issues/66
     */
    public function refTestIssue66(): void
    {
        $code = <<<CODE
        <?php

        declare(strict_types=1);

        namespace App;

        final class SomeClass
        {

            /**
             * @return array{
             *     KeyName1?: non-empty-list<non-empty-string>,
             *     KeyName2?: non-empty-list<non-empty-string>,
             *     KeyName3?: non-empty-list<non-empty-string>,
             *     KeyName4?: non-empty-list<non-empty-string>,
             *     KeyName5?: non-empty-list<non-empty-string>,
             *     KeyName6?: non-empty-list<non-empty-string>,
             *     KeyName7?: non-empty-list<non-empty-string>,
             *     KeyName8?: non-empty-list<non-empty-string>,
             *     KeyName9?: non-empty-list<non-empty-string>,
             *     KeyName10?: non-empty-list<non-empty-string>,
             *     KeyName11?: non-empty-list<non-empty-string>,
             *     KeyName12?: non-empty-list<non-empty-string>,
             *     KeyName13?: non-empty-list<non-empty-string>,
             *     KeyName14?: non-empty-string,
             *     KeyName15?: non-empty-string,
             *     KeyName16?: non-empty-string,
             *     KeyName17?: non-empty-string,
             *     KeyName18?: non-empty-string,
             *     KeyName19?: non-empty-string,
             *     KeyName20?: non-empty-string,
             *     KeyName21?: non-empty-string,
             *     KeyName22?: non-empty-string,
             *     KeyName23?: non-empty-string,
             *     KeyName24?: non-empty-string,
             *     KeyName25?: non-empty-string,
             *     KeyName26?: non-empty-string
             * }
             */
            public function someMethod(): array
            {
                return [];
            }
        }
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertSame(['non-empty-list', 'non-empty-string'], $symbols);
    }
}
