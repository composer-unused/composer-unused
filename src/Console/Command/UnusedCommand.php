<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Console\Command;

use Composer\Command\BaseCommand;
use Icanhazstring\Composer\Unused\Command\CollectConsumedSymbolsCommand;
use Icanhazstring\Composer\Unused\Command\FilterDependencyCollectionCommand;
use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\LoadRequiredDependenciesCommand;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\DependencyInterface;
use Icanhazstring\Composer\Unused\Dependency\InvalidDependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

use const DIRECTORY_SEPARATOR;

final class UnusedCommand extends BaseCommand
{
    /** @var CollectConsumedSymbolsCommandHandler */
    private $collectConsumedSymbolsCommandHandler;
    /** @var CollectRequiredDependenciesCommandHandler */
    private $collectRequiredDependenciesCommandHandler;
    /** @var CollectFilteredDependenciesCommandHandler */
    private $collectFilteredDependenciesCommandHandler;

    public function __construct(
        CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler,
        CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler,
        CollectFilteredDependenciesCommandHandler $collectFilteredDependenciesCommandHandler
    ) {
        parent::__construct('unused');
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
        $this->collectRequiredDependenciesCommandHandler = $collectRequiredDependenciesCommandHandler;
        $this->collectFilteredDependenciesCommandHandler = $collectFilteredDependenciesCommandHandler;
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
            )
        );

        $unfilteredRequiredDependencyCollection = $this->collectRequiredDependenciesCommandHandler->collect(
            new LoadRequiredDependenciesCommand(
                $baseDir . DIRECTORY_SEPARATOR . $composer->getConfig()->get('vendor-dir'),
                $rootPackage->getRequires(),
                $composer->getRepositoryManager()->getLocalRepository()
            )
        );

        $requiredDependencyCollection = $this->collectFilteredDependenciesCommandHandler->collect(
            new FilterDependencyCollectionCommand(
                $unfilteredRequiredDependencyCollection,
                $input->getOption('excludePackage'),
                [] // TODO use pattern exclude option from command line
            )
        );

        $io = new SymfonyStyle($input, $output);

        foreach ($consumedSymbols as $symbol) {
            /** @var RequiredDependency $requiredDependency */
            foreach ($requiredDependencyCollection as $requiredDependency) {
                if ($requiredDependency->inState($requiredDependency::STATE_USED)) {
                    continue;
                }

                if ($requiredDependency->getName() === 'php' || $requiredDependency->provides($symbol)) {
                    $requiredDependency->markUsed();
                    continue;
                }
                $relatedRequiredDependencies  = [];
                /** @var RequiredDependency $secondRequiredDependency */
                foreach ($requiredDependencyCollection as $secondRequiredDependency) {
                    if ($requiredDependency === $secondRequiredDependency) {
                        continue;
                    }

                    if ($secondRequiredDependency->requires($requiredDependency)) {
                        // TODO add "required by" in output
                        $relatedRequiredDependencies[$secondRequiredDependency->getName()] = $requiredDependency->getName();
                        $requiredDependency->markUsed();
                        continue 2;
                    }

                    if ($secondRequiredDependency->suggests($requiredDependency)) {
                        // TODO add "suggested by" in output
                        $requiredDependency->markUsed();
                        continue 2;
                    }
                }
            }
        }

        [$usedDependencyCollection, $unusedDependencyCollection] = $requiredDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_USED);
            }
        );

        /** @var DependencyCollection<InvalidDependency> $invalidDependencyCollection */
        [$invalidDependencyCollection, $unusedDependencyCollection] = $unusedDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_INVALID);
            }
        );

        $io->section('Results');

        $io->writeln(
            sprintf(
                'Found <fg=green>%d used</>, <fg=red>%d unused</> and <fg=yellow>%d ignored</> packages',
                count($usedDependencyCollection),
                count($unusedDependencyCollection),
                count($invalidDependencyCollection)
            )
        );

        $io->newLine();
        $io->text('<fg=green>Used packages</>');
        foreach ($usedDependencyCollection as $usedDependency) {
            $message =  sprintf(
                ' <fg=green>%s</> %s',
                "\u{2713}",
                $usedDependency->getName()
            );
            if (isset($relatedRequiredDependencies[$usedDependency->getName()])) {
                $message = sprintf(
                    ' <fg=green>%s</> %s (required by %s)',
                    "\u{2713}",
                    $usedDependency->getName(),
                    $relatedRequiredDependencies[$usedDependency->getName()]
                );
            }
            // TODO add suggest by dependency
            $io->writeln($message);
        }

        $io->newLine();
        $io->text('<fg=red>Unused packages</>');
        foreach ($unusedDependencyCollection as $dependency) {
            $io->writeln(
                sprintf(
                    ' <fg=red>%s</> %s',
                    "\u{2717}",
                    $dependency->getName()
                )
            );
        }

        $io->newLine();
        $io->text('<fg=yellow>Ignored packages</>');

        foreach ($invalidDependencyCollection as $dependency) {
            $io->writeln(
                sprintf(
                    ' <fg=yellow>%s</> %s (<fg=cyan>%s</>)',
                    "\u{25CB}",
                    $dependency->getName(),
                    $dependency->getReason()
                )
            );
        }

        if ($unusedDependencyCollection->count() > 0) {
            return 1;
        }

        return 0;
    }
}
