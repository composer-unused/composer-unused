<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Parser;

use Exception;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\ParseStrategyInterface;
use Icanhazstring\Composer\Unused\Parser\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use SplFileInfo;

class NodeVisitorTest extends TestCase
{
    public function itShouldParseUsagesDataProvider(): array
    {
        return [
            'StaticParseStrategyShouldReturnEmptyUsageOnVariableCall'  => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticVariableCall.php',
                'strategy'               => new StaticParseStrategy()
            ],
            'StaticParseStrategyShouldReturnEmptyUsageOnNonFQCall'     => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticNonFullyQualifiedCall.php',
                'strategy'               => new StaticParseStrategy()
            ],
            'StaticParseStrategyShouldReturnCorrectNamespaceOnFQCall'      => [
                'expectedUsedNamespaces' => [
                    'StaticFullyQualifiedCall'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticFullyQualifiedCall.php',
                'strategy'               => new StaticParseStrategy()
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnDynamicClassnameCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateDynamicClass.php',
                'strategy'               => new NewParseStrategy()
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnNonFQCall'        => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateNonFullyQualifiedCall.php',
                'strategy'               => new NewParseStrategy()
            ],
            'NewParseStrategyShouldReturnCorrectNamespaceOnFQCall'     => [
                'expectedUsedNamespaces' => [
                    'NewInstantiateFullyQualifiedCall'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateFullyQualifiedCall.php',
                'strategy'               => new NewParseStrategy()
            ],
            'UseParseStrategyShouldReturnSingleLineImportedNamespaces' => [
                'expectedUsedNamespaces' => [
                    'Icanhazstring\Composer',
                    'Icanhazstring\Composer\Unused\Parser',
                    'Icanhazstring\Composer\Unused\Command'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/UseSingleLineNoGroup.php',
                'strategy'               => new UseParseStrategy()
            ],
            'UseParseStrategyShouldReturnMultiLineImportedNamespaces'  => [
                'expectedUsedNamespaces' => [
                    UseParseStrategy::class,
                    StaticParseStrategy::class,
                    NewParseStrategy::class
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/UseMultiLineGroup.php',
                'strategy'               => new UseParseStrategy()
            ],
            'ClassConstStrategyShouldReturnCorrectNamespace'           => [
                'expectedUsedNamespaces' => [
                    UseParseStrategy::class,
                    StaticParseStrategy::class
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/ClassConst.php',
                'strategy'               => new ClassConstStrategy()
            ],
            'NewParseStrategyShouldReturnQualifiedNamespace'           => [
                'expectedUsedNamespaces' => [
                    'TestFile\NewInstantiateQualifiedClass'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateQualifiedClass.php',
                'strategy'               => new NewParseStrategy()
            ],
            'StaticParseStrategyShouldReturnQualifiedNamespace'        => [
                'expectedUsedNamespaces' => [
                    'TestFile\StaticQualifiedCall'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticQualifiedCall.php',
                'strategy'               => new StaticParseStrategy()
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---1'        => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithCustomInterfaceName.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---2'        => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithCustomInterface.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---3'        => [
                'expectedUsedNamespaces' => ['json'],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithExtensionInterface.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---4'        => [
                'expectedUsedNamespaces' => ['json'],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithExtensionInterfaceInUse.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---5'        => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithCustomConstant.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---6'        => [
                'expectedUsedNamespaces' => ['json'],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithJsonConstant.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---7'        => [
                'expectedUsedNamespaces' => ['json'],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithExtensionFunction.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
            'PhpExtensionParseStrategyShouldReturnQualifiedNamespace---8'        => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/PhpExtensionStrategy/ClassWithCustomFunction.php',
                'strategy'               => new PhpExtensionStrategy(['json'])
            ],
        ];
    }

    /**
     * @test
     * @param array  $expectedUsedNamespaces
     * @param string $inputFile
     * @param ParseStrategyInterface $strategy
     * @dataProvider itShouldParseUsagesDataProvider
     */
    public function itShouldParseUsages(array $expectedUsedNamespaces, string $inputFile, ParseStrategyInterface $strategy): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        /** @var string $contents */
        $contents = file_get_contents($inputFile);
        /** @var Node[] $nodes */
        $nodes = $parser->parse($contents);

        $nodeVisitor = new NodeVisitor([$strategy], $this->prophesize(ErrorHandlerInterface::class)->reveal());
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
        /** @var string $contents */
        $inputFile = __DIR__ . '/../../assets/TestFiles/UseSingleLineNoGroup.php';
        /** @var string $contents */
        $contents = file_get_contents($inputFile);
        /** @var Node[] $nodes */
        $nodes = $parser->parse($contents);

        $exception = new Exception('');

        $errorHandler = $this->prophesize(ErrorHandlerInterface::class);
        $errorHandler->handle($exception)->shouldBeCalled();

        $exceptionParseStrategy = $this->prophesize(UseParseStrategy::class);

        /** @var Node $node */
        $node = Argument::any();
        $exceptionParseStrategy->meetsCriteria($node)->willReturn(true);
        $exceptionParseStrategy->extractNamespaces($node)->willThrow($exception);

        $nodeVisitor = new NodeVisitor([$exceptionParseStrategy->reveal()], $errorHandler->reveal());
        $fileInfo = new SplFileInfo($inputFile);
        $nodeVisitor->setCurrentFile($fileInfo);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);

        $traverser->traverse($nodes);
    }
}
