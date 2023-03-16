<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\ConfigurationProvider;
use ComposerUnused\ComposerUnused\Console\Command\DebugConsumedSymbolsCommand;
use ComposerUnused\ComposerUnused\Console\Command\DebugProvidedSymbolsCommand;
use ComposerUnused\ComposerUnused\Console\Command\UnusedCommand;
use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\AnnotationStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ExtendsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FullQualifiedParameterStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FunctionInvocationStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ImplementsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\InstanceofStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\TypedAttributeStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UsedExtensionSymbolStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\CiDetectorInterface;
use PhpParser\Lexer\Emulative;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

require_once 'polyfill_symfony_di.php';

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$consumedSymbolCollector', service('collector.consumed'));

    $vendorDir = dirname(UNUSED_COMPOSER_INSTALL);

    $nameSpacePrefix = '';
    if (__NAMESPACE__ !== '') {
        $nameSpacePrefix = __NAMESPACE__ . '\\';
    }

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services
        ->load($nameSpacePrefix . 'ComposerUnused\\ComposerUnused\\', __DIR__ . '/../src/*')
        ->load($nameSpacePrefix . 'ComposerUnused\\SymbolParser\\', $vendorDir . '/composer-unused/symbol-parser/src/*');

    $services->set(UnusedCommand::class)->public();
    $services->set(DebugConsumedSymbolsCommand::class)->public();
    $services->set(DebugProvidedSymbolsCommand::class)->public();

    $services->set('logger', NullLogger::class);

    $services->set(ConfigurationProvider::class);

    $services->set(UsedExtensionSymbolStrategy::class)->args([
        get_loaded_extensions(),
        service('logger')
    ]);

    $services
        ->set('collector.consumed', ConsumedSymbolCollector::class)
        ->arg('$strategies', [
            service(UseStrategy::class),
            service(ExtendsParseStrategy::class),
            service(ImplementsParseStrategy::class),
            service(TypedAttributeStrategy::class),
            service(FullQualifiedParameterStrategy::class),
            service(ClassConstStrategy::class),
            service(ConstStrategy::class),
            service(FunctionInvocationStrategy::class),
            service(InstanceofStrategy::class),
            service(NewStrategy::class),
            service(StaticStrategy::class),
            service(UsedExtensionSymbolStrategy::class),
            service(AnnotationStrategy::class),
        ]);

    $services->set(CiDetectorInterface::class, CiDetector::class);

    $services
        ->set(ConstExprParser::class, ConstExprParser::class)
        ->set(Lexer::class, Lexer::class);

    $lexerVersion = Emulative::PHP_8_1;

    if (PHP_VERSION_ID < 80000) {
        $lexerVersion = Emulative::PHP_7_4;
    } elseif (PHP_VERSION_ID < 80100) {
        $lexerVersion = Emulative::PHP_8_0;
    }

    $services->set(Emulative::class)->arg('$options', ['phpVersion' => $lexerVersion]);
};
