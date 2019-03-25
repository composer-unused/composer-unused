<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Psr\Container\ContainerInterface;

final class UnusedPlugin implements Plugin\PluginInterface, Plugin\Capable, Plugin\Capability\CommandProvider
{
    /** @var ContainerInterface */
    private $container;

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

        $this->container->setService(IOInterface::class, $io);
        $this->container->setService(Composer::class, $composer);
    }

    public function getCapabilities()
    {
        return [
            Plugin\Capability\CommandProvider::class => self::class
        ];
    }

    /**
     * @return array|\Composer\Command\BaseCommand[]
     * @throws \Exception
     */
    public function getCommands(): array
    {
        return [
            $this->container->get(UnusedCommand::class)
        ];
    }
}
