<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Parser;

use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class NodeVisitorTest extends TestCase
{
    public function itShouldParseUsagesDataProvider(): array
    {
        return [
            'StaticParseStrategyShouldReturnEmptyUsageOnVariableCall'      => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticVariableCall.php',
                'strategy'               => StaticParseStrategy::class
            ],
            'StaticParseStrategyShouldReturnEmptyUsageOnNonFQCall'         => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticNonFullyQualifiedCall.php',
                'strategy'               => StaticParseStrategy::class
            ],
            'StaticParseStrategyShouldReturnCorrectNamespaceOnFQCall'      => [
                'expectedUsedNamespaces' => [
                    'StaticFullyQualifiedCall'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/StaticFullyQualifiedCall.php',
                'strategy'               => StaticParseStrategy::class
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnDynamicClassnameCall' => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateDynamicClass.php',
                'strategy'               => NewParseStrategy::class
            ],
            'NewParseStrategyShouldReturnEmptyUsageOnNonFQCall'            => [
                'expectedUsedNamespaces' => [],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateNonFullyQualifiedCall.php',
                'strategy'               => NewParseStrategy::class
            ],
            'NewParseStrategyShouldReturnCorrectNamespaceOnFQCall'         => [
                'expectedUsedNamespaces' => [
                    'NewInstantiateFullyQualifiedCall'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/NewInstantiateFullyQualifiedCall.php',
                'strategy'               => NewParseStrategy::class
            ],
            'UseParseStrategyShouldReturnSingleLineImportedNamespaces'     => [
                'expectedUsedNamespaces' => [
                    'Icanhazstring\Composer',
                    'Icanhazstring\Composer\Unused\Parser',
                    'Icanhazstring\Composer\Unused\Command'
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/UseSingleLineNoGroup.php',
                'strategy'               => UseParseStrategy::class
            ],
            'UseParseStrategyShouldReturnMultiLineImportedNamespaces'      => [
                'expectedUsedNamespaces' => [
                    UseParseStrategy::class,
                    StaticParseStrategy::class,
                    NewParseStrategy::class
                ],
                'inputFile'              => __DIR__ . '/../../assets/TestFiles/UseMultiLineGroup.php',
                'strategy'               => UseParseStrategy::class
            ]
        ];
    }

    /**
     * @test
     * @param array  $expectedUsedNamespaces
     * @param string $inputFile
     * @param string $strategy
     * @dataProvider itShouldParseUsagesDataProvider
     */
    public function itShouldParseUsages(array $expectedUsedNamespaces, string $inputFile, string $strategy): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        /** @var string $contents */
        $contents = file_get_contents($inputFile);
        /** @var Node[] $nodes */
        $nodes = $parser->parse($contents);

        $nodeVisitor = new NodeVisitor([new $strategy()]);
        $fileInfo = new \SplFileInfo($inputFile);
        $nodeVisitor->setCurrentFile($fileInfo);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);

        $traverser->traverse($nodes);
        $this->assertEquals($expectedUsedNamespaces, array_keys($nodeVisitor->getUsages()));
    }
}
