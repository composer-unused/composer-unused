<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\UseCase;

use Composer\Composer;
use Composer\Config;
use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\Loader\SymbolLoaderInterface;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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
    public function itRemoveRootNamespaceSymbols(): void
    {
        $rootPackage = $this->prophesize(PackageInterface::class);
        $rootPackage->getAutoload()->willReturn(['psr-4' => ['Test\\' => 'src']]);

        $configSource = $this->prophesize(Config\ConfigSourceInterface::class);
        $configSource->getName()->willReturn(__DIR__);

        $composerConfig = $this->prophesize(Config::class);
        $composerConfig->getConfigSource()->willReturn($configSource->reveal());

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage);
        $composer->getConfig()->willReturn($composerConfig->reveal());

        $symbolLoader = $this->prophesize(SymbolLoaderInterface::class);
        $symbolLoader->load(Argument::any())->willReturn(
            $this->arrayAsGenerator([
                new Symbol('Test\\Sub\\Classname'),
                new Symbol('Test\\SecondClassname'),
                new Symbol('ShouldStay\\Classname')
            ])
        );

        $useCase = new CollectUsedSymbolsUseCase($symbolLoader->reveal());
        /** @var array<SymbolInterface> $symbols */
        $symbols = iterator_to_array($useCase->execute($composer->reveal()));

        $symbol = current($symbols);

        self::assertCount(1, $symbols);
        self::assertSame('ShouldStay\\Classname', $symbol->getIdentifier());
    }
}
