<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\AutoloadType;
use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ExtendsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FunctionInvocationStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ImplementsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\InstanceofStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UsedExtensionSymbolStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use PhpParser\ParserFactory;
use Psr\Log\NullLogger;

use function get_loaded_extensions;

final class ConsumedSymbolLoaderBuilder
{
    public function build(): SymbolLoaderInterface
    {
        $usedSymbolCollector = new ConsumedSymbolCollector(
            [
                new ClassConstStrategy(),
                new ConstStrategy(),
                new ExtendsParseStrategy(),
                new FunctionInvocationStrategy(),
                new ImplementsParseStrategy(),
                new InstanceofStrategy(),
                new NewStrategy(),
                new StaticStrategy(),
                new UsedExtensionSymbolStrategy(
                    get_loaded_extensions(),
                    // TODO logger
                    new NullLogger()
                ),
                new UseStrategy(),
            ]
        );

        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $usedSymbolCollector
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        return new FileSymbolLoader(
            $fileSymbolProvider,
            AutoloadType::all()
        );
    }
}
