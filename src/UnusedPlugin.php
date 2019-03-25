<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin;

final class UnusedPlugin implements Plugin\PluginInterface, Plugin\Capable, Plugin\Capability\CommandProvider
{
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
            new Command\UnusedCommand()
        ];
    }
}
