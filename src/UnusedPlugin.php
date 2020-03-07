<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin;
use Exception;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;

final class UnusedPlugin implements Plugin\PluginInterface, Plugin\Capable, Plugin\Capability\CommandProvider
{
    public const VERSION = '0.7.0';

    /** @var ServiceContainer */
    private $container;

    /**
     * @param mixed ...$args
     */
    public function __construct(...$args)
    {
        if (!empty($args)) {
            /** @var self $plugin */
            $plugin = $args[0]['plugin'];

            $this->container = $plugin->container;
        }
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->container = require __DIR__ . '/../config/container.php';

        $this->container->register(IOInterface::class, $io);
        $this->container->register(Composer::class, $composer);
    }

    public function getCapabilities()
    {
        return [
            Plugin\Capability\CommandProvider::class => self::class
        ];
    }

    /**
     * @return array|BaseCommand[]
     * @throws Exception
     */
    public function getCommands(): array
    {
        return [
            $this->container->get(UnusedCommand::class)
        ];
    }
}
