<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Console\Command;

use Icanhazstring\Composer\Unused\Command\CollectConsumedSymbolsCommand;
use Icanhazstring\Composer\Unused\Command\FilterDependencyCollectionCommand;
use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\LoadRequiredDependenciesCommand;
use Icanhazstring\Composer\Unused\Composer\ConfigFactory;
use Icanhazstring\Composer\Unused\Composer\LocalRepository;
use Icanhazstring\Composer\Unused\Composer\Package;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\DependencyInterface;
use Icanhazstring\Composer\Unused\Dependency\InvalidDependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

use const DIRECTORY_SEPARATOR;

final class UnusedCommand extends Command
{
    /** @var CollectConsumedSymbolsCommandHandler */
    private $collectConsumedSymbolsCommandHandler;
    /** @var CollectRequiredDependenciesCommandHandler */
    private $collectRequiredDependenciesCommandHandler;
    /** @var CollectFilteredDependenciesCommandHandler */
    private $collectFilteredDependenciesCommandHandler;
    private ConfigFactory $configFactory;

    public function __construct(
        ConfigFactory $configFactory,
        CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler,
        CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler,
        CollectFilteredDependenciesCommandHandler $collectFilteredDependenciesCommandHandler
    ) {
        parent::__construct('unused');
        $this->configFactory = $configFactory;
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
        $this->collectRequiredDependenciesCommandHandler = $collectRequiredDependenciesCommandHandler;
        $this->collectFilteredDependenciesCommandHandler = $collectFilteredDependenciesCommandHandler;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning and comparing package namespaces against your source.'
        );

        $this->addArgument(
            'composer-json',
            InputArgument::OPTIONAL,
            'Provide a composer.json to be scanned',
            getcwd() . DIRECTORY_SEPARATOR . 'composer.json'
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
        $composerJsonPath = $input->getArgument('composer-json');

        if (!file_exists($composerJsonPath) && !is_readable($composerJsonPath)) {
            return 1;
        }

        $composerJson = file_get_contents($composerJsonPath);

        if ($composerJson === false) {
            return 1;
        }

        $config = $this->configFactory->fromComposerJson($composerJson);
        $baseDir = dirname($composerJsonPath);

        $rootPackage = Package::fromConfig($config);
        $localRepository = new LocalRepository($baseDir . DIRECTORY_SEPARATOR . $config->get('vendor-dir'));

        $consumedSymbols = $this->collectConsumedSymbolsCommandHandler->collect(
            new CollectConsumedSymbolsCommand(
                $baseDir,
                $rootPackage
            )
        );

        $unfilteredRequiredDependencyCollection = $this->collectRequiredDependenciesCommandHandler->collect(
            new LoadRequiredDependenciesCommand(
                $baseDir . DIRECTORY_SEPARATOR . $config->get('vendor-dir'),
                $rootPackage->getRequires(),
                $localRepository
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

                /** @var RequiredDependency $secondRequiredDependency */
                foreach ($requiredDependencyCollection as $secondRequiredDependency) {
                    if ($requiredDependency === $secondRequiredDependency) {
                        continue;
                    }

                    if ($secondRequiredDependency->requires($requiredDependency)) {
                        $requiredDependency->requiredBy($secondRequiredDependency);
                        $requiredDependency->markUsed();
                        continue 2;
                    }

                    if ($secondRequiredDependency->suggests($requiredDependency)) {
                        $requiredDependency->suggestedBy($secondRequiredDependency);
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
            $requiredBy = '';
            $suggestedBy = '';

            if (!empty($usedDependency->getRequiredBy())) {
                $requiredByNames = array_map(static function (DependencyInterface $dependency) {
                    return $dependency->getName();
                }, $usedDependency->getRequiredBy());

                $requiredBy = sprintf(
                    ' (<fg=cyan>required by: %s</>)',
                    implode(', ', $requiredByNames)
                );
            }

            if (!empty($usedDependency->getSuggestedBy())) {
                $suggestedByNames = array_map(static function (DependencyInterface $dependency) {
                    return $dependency->getName();
                }, $usedDependency->getSuggestedBy());

                $requiredBy = sprintf(
                    ' (<fg=cyan>suggested by: %s</>)',
                    implode(', ', $suggestedByNames)
                );
            }

            $io->writeln(
                sprintf(
                    ' <fg=green>%s</> %s%s%s',
                    "\u{2713}",
                    $usedDependency->getName(),
                    $requiredBy,
                    $suggestedBy
                )
            );
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
