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
    /** @var Composer */
    private $composer;
    private $version;

    public function __construct(...$args)
    {
        if (!empty($args)) {
            /** @var self $plugin */
            $plugin = $args[0]['plugin'];

            $this->composer = $plugin->composer;
            $this->isDebug = $plugin->isDebug;
            $this->version = $plugin->version;
        }
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->version = $composer->getPackage()->getPrettyVersion();
        $this->composer = $composer;
        $this->isDebug = $io->isDebug();
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
        $dumpFileName = 'composer-unused-dump-' . (new \DateTime())->format('YmdHis');

        $dumper = $this->isDebug
            ? new FileDumper($dumpFileName, $this->version, $this->composer)
            : new NullDumper();

        return [
            new Command\UnusedCommand(
                $this->isDebug ? new CollectingErrorHandler() : new ThrowingErrorHandler(),
                $dumper,
                new SymfonyStyleFactory()
            )
        ];
    }
}
