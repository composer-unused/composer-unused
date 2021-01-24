<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\UseCase;

use Composer\Package\RootPackage;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function iterator_to_array;

class CollectUsedSymbolsUseCaseTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @param array<mixed> $values
     * @return Generator<mixed>
     */
    private function arrayAsGenerator(array $values): Generator
    {
        yield from $values;
    }

    /**
     * @test
     */
    public function itShouldRemoveRootNamespaceSymbols(): void
    {
        $rootPackage = new RootPackage('test/package', '0.1.0', '0.1.0');
        $rootPackage->setAutoload(['psr-4' => ['Test\\' => 'src']]);

        $fileSymbolLoader = $this->createMock(FileSymbolLoader::class);
        $fileSymbolLoader->method('load')->willReturn(
            $this->arrayAsGenerator([
                new Symbol('Test\\Sub\\Classname'),
                new Symbol('Test\\SecondClassname'),
                new Symbol('ShouldStay\\Classname')
            ])
        );

        $useCase = new CollectUsedSymbolsUseCase($fileSymbolLoader);
        $symbols = iterator_to_array($useCase->execute($rootPackage, dirname(__DIR__)));
        /** @var SymbolInterface $symbol */
        $symbol = current($symbols);

        self::assertCount(1, $symbols);
        self::assertSame('ShouldStay\\Classname', $symbol->getIdentifier());
    }
}
