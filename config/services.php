<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Console\Command\UnusedCommand;
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
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\CiDetectorInterface;
use PhpParser\Lexer\Emulative;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$consumedSymbolCollector', service('collector.consumed'));

    $vendorDir = dirname(UNUSED_COMPOSER_INSTALL);

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services
        ->load('ComposerUnused\\ComposerUnused\\', __DIR__ . '/../src/*')
        ->load('ComposerUnused\\SymbolParser\\', $vendorDir . '/composer-unused/symbol-parser/src/*');

    $services->set(UnusedCommand::class)->public();

    $services->set('logger', NullLogger::class);

    $services->set(UsedExtensionSymbolStrategy::class)->args([
        get_loaded_extensions(),
        service('logger')
    ]);

    $services
        ->set('collector.consumed', ConsumedSymbolCollector::class)
        ->arg('$strategies', [
            service(ClassConstStrategy::class),
            service(ConstStrategy::class),
            service(ExtendsParseStrategy::class),
            service(FunctionInvocationStrategy::class),
            service(ImplementsParseStrategy::class),
            service(InstanceofStrategy::class),
            service(NewStrategy::class),
            service(StaticStrategy::class),
            service(UsedExtensionSymbolStrategy::class),
            service(UseStrategy::class)
        ]);

    $services->set(CiDetectorInterface::class, CiDetector::class);

    $lexerVersion = Emulative::PHP_8_1;

    if (PHP_VERSION_ID < 80000) {
        $lexerVersion = Emulative::PHP_7_4;
    } elseif (PHP_VERSION_ID < 81000) {
        $lexerVersion = Emulative::PHP_8_0;
    }

    $services->set(Emulative::class)->arg('$options', ['phpVersion' => $lexerVersion]);
};
