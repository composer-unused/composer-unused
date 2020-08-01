<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Parser\PHP;

use Exception;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ExtendsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ImplementsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\NewStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StrategyInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StaticStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\UseStrategy;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;
use SplFileInfo;

use const ASSET_DIR;

class NodeVisitorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function itShouldParseUsagesDataProvider(): array
    {
        return [
            'StaticParseStrategyShouldReturnEmptyUsageOnVariableCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/StaticVariableCall.php',
                'strategy' => new StaticStrategy()
            ],
            'StaticParseStrategyShouldReturnEmptyUsageOnNonFQCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/StaticNonFullyQualifiedCall.php',
                'strategy' => new StaticStrategy()
            ],
            'StaticParseStrategyShouldReturnCorrectNamespaceOnFQCall' => [
                'expectedUsedNamespaces' => [
                    'StaticFullyQualifiedCall'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/StaticFullyQualifiedCall.php',
                'strategy' => new StaticStrategy()
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnDynamicClassnameCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/NewInstantiateDynamicClass.php',
                'strategy' => new NewStrategy()
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnNonFQCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/NewInstantiateNonFullyQualifiedCall.php',
                'strategy' => new NewStrategy()
            ],
            'NewParseStrategyShouldReturnCorrectNamespaceOnFQCall' => [
                'expectedUsedNamespaces' => [
                    'NewInstantiateFullyQualifiedCall'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/NewInstantiateFullyQualifiedCall.php',
                'strategy' => new NewStrategy()
            ],
            'UseParseStrategyShouldReturnSingleLineImportedNamespaces' => [
                'expectedUsedNamespaces' => [
                    'Icanhazstring\Composer',
                    'Icanhazstring\Composer\Unused\Parser\PHP',
                    'Icanhazstring\Composer\Unused\Command'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/UseSingleLineNoGroup.php',
                'strategy' => new UseStrategy()
            ],
            'UseParseStrategyShouldReturnMultiLineImportedNamespaces' => [
                'expectedUsedNamespaces' => [
                    UseStrategy::class,
                    StaticStrategy::class,
                    NewStrategy::class
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/UseMultiLineGroup.php',
                'strategy' => new UseStrategy()
            ],
            'ClassConstStrategyShouldReturnCorrectNamespace' => [
                'expectedUsedNamespaces' => [
                    UseStrategy::class,
                    StaticStrategy::class
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/ClassConst.php',
                'strategy' => new ClassConstStrategy()
            ],
            'NewParseStrategyShouldReturnQualifiedNamespace' => [
                'expectedUsedNamespaces' => [
                    'TestFile\NewInstantiateQualifiedClass'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/NewInstantiateQualifiedClass.php',
                'strategy' => new NewStrategy()
            ],
            'StaticParseStrategyShouldReturnQualifiedNamespace' => [
                'expectedUsedNamespaces' => [
                    'TestFile\StaticQualifiedCall'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/StaticQualifiedCall.php',
                'strategy' => new StaticStrategy()
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---1' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithCustomInterfaceName.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---2' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithCustomInterface.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---3' => [
                'expectedUsedNamespaces' => ['ext-json'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithExtensionInterface.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---4' => [
                'expectedUsedNamespaces' => ['ext-json'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithExtensionInterfaceInUse.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---5' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithCustomConstant.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---6' => [
                'expectedUsedNamespaces' => ['ext-json'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithJsonConstant.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---7' => [
                'expectedUsedNamespaces' => ['ext-json'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithExtensionFunction.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---8' => [
                'expectedUsedNamespaces' => [],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithCustomFunction.php',
                'strategy' => new PhpExtensionStrategy(['json'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---9' => [
                'expectedUsedNamespaces' => ['ext-zend-opcache'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithZendOpcache.php',
                'strategy' => new PhpExtensionStrategy(['Zend Opcache'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---10' => [
                'expectedUsedNamespaces' => ['php'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithCore.php',
                'strategy' => new PhpExtensionStrategy(['Core'], new NullLogger())
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---11' => [
                'expectedUsedNamespaces' => ['ext-ds'],
                'inputFile' => ASSET_DIR . '/TestFiles/PhpExtensionStrategy/ClassWithDs.php',
                'strategy' => new PhpExtensionStrategy(['ds'], new NullLogger())
            ],
            'ClassExtendsFQN' => [
                'expectedUseNamespaces' => ['A\B'],
                'inputFile' => ASSET_DIR . '/TestFiles/ClassExtendsFQN.php',
                'strategy' => new ExtendsParseStrategy()
            ],
            'ClassImplements' => [
                'expectedUseNamespaces' => [
                    'A\InterfaceA',
                    'B\InterfaceB'
                ],
                'inputFile' => ASSET_DIR . '/TestFiles/ClassImplements.php',
                'strategy' => new ImplementsParseStrategy()
            ]
        ];
    }

    /**
     * @test
     * @param array<string> $expectedUsedNamespaces
     * @param string $inputFile
     * @param StrategyInterface $strategy
     * @dataProvider itShouldParseUsagesDataProvider
     */
    public function itShouldParseUsages(
        array $expectedUsedNamespaces,
        string $inputFile,
        StrategyInterface $strategy
    ): void {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        /** @var string $contents */
        $contents = file_get_contents($inputFile);
        /** @var Node[] $nodes */
        $nodes = $parser->parse($contents);

        $nodeVisitor = new NamespaceNodeVisitor([$strategy], $this->prophesize(ErrorHandlerInterface::class)->reveal());
        $fileInfo = new SplFileInfo($inputFile);
        $nodeVisitor->setCurrentFile($fileInfo);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);

        $traverser->traverse($nodes);
        $this->assertEquals($expectedUsedNamespaces, array_keys($nodeVisitor->getUsages()));
    }

    /**
     * @test
     */
    public function itShouldRaiseExceptionHandledByErrorHandler(): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        /** @var string $inputFile */
        $inputFile = ASSET_DIR . '/TestFiles/UseSingleLineNoGroup.php';
        /** @var string $contents */
        $contents = file_get_contents($inputFile);
        /** @var Node[] $nodes */
        $nodes = $parser->parse($contents);

        $exception = new Exception('');

        $errorHandler = $this->prophesize(ErrorHandlerInterface::class);
        $errorHandler->handle($exception)->shouldBeCalled();

        $exceptionParseStrategy = $this->prophesize(UseStrategy::class);

        /** @var Node $node */
        $node = Argument::any();
        $exceptionParseStrategy->canHandle($node)->willReturn(true);
        $exceptionParseStrategy->extractSymbolNames($node)->willThrow($exception);

        $nodeVisitor = new NamespaceNodeVisitor([$exceptionParseStrategy->reveal()], $errorHandler->reveal());
        $fileInfo = new SplFileInfo($inputFile);
        $nodeVisitor->setCurrentFile($fileInfo);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);

        $traverser->traverse($nodes);
    }
}
