<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin;
use Icanhazstring\Composer\Unused\Error\FileDumper;
use Icanhazstring\Composer\Unused\Error\Handler\CollectingErrorHandler;
use Icanhazstring\Composer\Unused\Error\Handler\ThrowingErrorHandler;
use Icanhazstring\Composer\Unused\Error\NullDumper;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;

final class UnusedPlugin implements Plugin\PluginInterface, Plugin\Capable, Plugin\Capability\CommandProvider
{
    private $isDebug;

    public function __construct(...$args)
    {
        if (!empty($args)) {
            /** @var IOInterface $io */
            $io = $args[0]['io'];
            $this->isDebug = $io->isDebug();
        }
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function getCapabilities()
    {
        return [
            Plugin\Capability\CommandProvider::class => self::class
        ];
    }

    public function getCommands()
    {
        return [
            new Command\UnusedCommand(
                $this->isDebug ? new CollectingErrorHandler() : new ThrowingErrorHandler(),
                $this->isDebug ? new FileDumper() : new NullDumper(),
                new SymfonyStyleFactory()
            )
        ];
    }
}
