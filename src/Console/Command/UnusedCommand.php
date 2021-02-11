<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Console\Command;

use Composer\Command\BaseCommand;
use Icanhazstring\Composer\Unused\Command\CollectConsumedSymbolsCommand;
use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;

final class UnusedCommand extends BaseCommand
{
    /** @var CollectConsumedSymbolsCommandHandler */
    private $collectConsumedSymbolsCommandHandler;

    public function __construct(CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler)
    {
        parent::__construct('unused');
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning and comparing package namespaces against your source.'
        );

        $this->addOption(
            'excludeDir',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Provide one or more folders to exclude from usage scan',
            []
        );

        $this->addOption(
            'excludePackage',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Provide one or more packages that should be ignored during scan',
            []
        );

        $this->addOption(
            'ignore-exit-code',
            null,
            InputOption::VALUE_NONE,
            'Ignore exit codes so there are no "failure" exit codes'
        );

        $this->addOption(
            'no-progress',
            null,
            InputOption::VALUE_NONE,
            'Show no progress bar'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = $this->getComposer();

        if ($composer === null) {
            // TODO IO Error
            return 1;
        }

        $baseDir = dirname($composer->getConfig()->getConfigSource()->getName());
        $rootPackage = $composer->getPackage();

        $consumedSymbols = $this->collectConsumedSymbolsCommandHandler->collect(
            new CollectConsumedSymbolsCommand(
                $baseDir,
                $rootPackage
                // TODO add excludes
            )
        );

        /**
        $providedSymbols = $this->collectProvidedSymbolsCommandHandler->collect(
            new CollectProvidedSymbolsCommand(
                $baseDir . DIRECTORY_SEPARATOR . $composer->getConfig()->get('vendor-dir'),
                $rootPackage->getRequires()
            )
        );
        */

        $requiredDependencyCollection = $this->loadRequiredDependenciesCommandHandler->execute(
            new LoadRequiredDependenciesCommand(
                $baseDir . DIRECTORY_SEPARATOR . $composer->getConfig()->get('vendor-dir'),
                $rootPackage->getRequires()
            )
        );

        return 0;
    }
}
