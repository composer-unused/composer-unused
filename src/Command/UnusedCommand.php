<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use Icanhazstring\Composer\Unused\UnusedPlugin;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class UnusedCommand extends BaseCommand
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var SymfonyStyleFactory */
    private $symfonyStyleFactory;
    /** @var SymfonyStyle */
    private $io;
    /** @var LoggerInterface */
    private $logger;
    /** @var LoaderBuilder */
    private $loaderBuilder;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        SymfonyStyleFactory $outputFactory,
        LoaderBuilder $loaderBuilder,
        LoggerInterface $logger
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->symfonyStyleFactory = $outputFactory;
        $this->loaderBuilder = $loaderBuilder;
        $this->logger = $logger;
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

        $this->addOption(
            'interactive',
            null,
            InputOption::VALUE_OPTIONAL,
            "Choose between remove, skip, and ignore",
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = ($this->symfonyStyleFactory)($input, $output);
        /** @var Composer|null $composer */
        $composer = $this->getComposer();

        if ($composer === null) {
            $this->io->error('Could not get composer dependency');
            return 1;
        }

        $this->logCommandInfo($composer);

        /** @var string[] $excludePackagesOption */
        $excludePackagesOption = $input->getOption('excludePackage');
        $excludePackagesConfig = $composer->getPackage()->getExtra()['unused'] ?? [];

        /** @var string[] $excludeDirs */
        $excludeDirs = $input->getOption('excludeDir');

        /** @var bool $noProgress */
        $noProgress = $input->getOption('no-progress');

        $packageLoaderResult = $this->loaderBuilder
            ->build(PackageLoader::class, array_merge($excludePackagesConfig, $excludePackagesOption))
            ->toggleProgress($noProgress)
            ->load($composer, $this->io);

        if (!$packageLoaderResult->hasItems()) {
            $this->io->success('Done. No required packages to scan.');
            return 0;
        }

        $usageLoaderResult = $this->loaderBuilder
            ->build(UsageLoader::class, $excludeDirs)
            ->toggleProgress($noProgress)
            ->load($composer, $this->io);
        $analyseUsageResult = $this->analyseUsages($packageLoaderResult->getItems(), $usageLoaderResult->getItems());

        /** @var PackageSubject[] $usedPackages */
        $usedPackages = $analyseUsageResult['used'];
        /** @var PackageSubject[] $unusedPackages */
        $unusedPackages = $analyseUsageResult['unused'];

        $this->io->section('Results');

        $this->io->writeln(
            sprintf(
                'Found <fg=green>%d used</>, <fg=red>%d unused</> and <fg=yellow>%d ignored</> packages',
                count($usedPackages),
                count($unusedPackages),
                count($packageLoaderResult->getSkippedItems())
            )
        );

        $this->io->newLine();
        $this->io->text('<fg=green>Used packages</>');
        foreach ($usedPackages as $package) {
            $requiredBy = '';
            $suggestedBy = '';

            if (!empty($package->getRequiredBy())) {
                $requiredBy = sprintf(
                    ' (<fg=cyan>required by: %s</>)',
                    implode(', ', $package->getRequiredBy())
                );
            }

            if (!empty($package->getSuggestedBy())) {
                $suggestedBy = sprintf(
                    ' (<fg=cyan>suggested by: %s</>)',
                    implode(', ', $package->getSuggestedBy())
                );
            }

            $this->io->writeln(
                sprintf(
                    ' <fg=green>%s</> %s%s%s',
                    "\u{2713}",
                    $package->getName(),
                    $requiredBy,
                    $suggestedBy
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=red>Unused packages</>');
        foreach ($unusedPackages as $package) {
            $this->io->writeln(
                sprintf(
                    ' <fg=red>%s</> %s',
                    "\u{2717}",
                    $package->getName()
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=yellow>Ignored packages</>');

        foreach ($packageLoaderResult->getSkippedItems() as $skippedItem) {
            $this->io->writeln(
                sprintf(
                    ' <fg=yellow>%s</> %s (<fg=cyan>%s</>)',
                    "\u{25CB}",
                    $skippedItem['item'],
                    $skippedItem['reason']
                )
            );
        }
        
        // If there is at least one unused package 
        if (count($unusedPackages) > 0 && !$input->getOption('ignore-exit-code')) {
            if ($input->getOption("interactive")) {
                $removeArray = [];

                foreach ($unusedPackages as $unusedPackage) {
                    $question = new ChoiceQuestion(
                        "Would you like to remove, skip, or ignore this package? (defaults to skip)",
                        ["remove", "skip", "ignore"],
                        1
                    );

                    $helper = $this->getHelper("question");
                    $action = $helper->ask($input, $output, $question);
                    
                    switch ($action) {
                        case "remove":
                            array_push($removeArray, $unusedPackage);
                        break;
                        case "skip":
                        break;
                        case "ignore":
                            
                        break;
                        default:
                            $output->writeLn("That action is invalid");
                        break;
                    }
                }
            }
            return 1;
        }

        return 0;
    }

    /**
     * @param PackageSubject[] $packages
     * @param UsageInterface[] $usages
     * @return array<string, array<PackageInterface|PackageSubject>>
     */
    private function analyseUsages(array $packages, array $usages): array
    {
        /** @var PackageInterface[] $unusedPackages */
        $unusedPackages = [];
        /** @var PackageInterface[] $usedPackages */
        $usedPackages = [];

        foreach ($packages as $package) {
            foreach ($usages as $usage) {
                try {
                    if (
                        $package->providesNamespace($usage->getNamespace())
                        || $package->getName() === $usage->getNamespace()
                    ) {
                        $usedPackages[] = $package;
                        continue 2;
                    }
                } catch (Throwable $throwable) {
                    $this->errorHandler->handle($throwable);
                }
            }

            foreach ($packages as $referencePackage) {
                $used = false;

                if ($referencePackage->suggestsPackage($package->getName())) {
                    $package->addSuggestedBy($referencePackage->getName());
                    $used = true;
                }

                if ($referencePackage->requiresPackage($package->getName())) {
                    $package->addRequiredBy($referencePackage->getName());
                    $used = true;
                }

                if ($used) {
                    $usedPackages[] = $package;
                    continue 2;
                }
            }

            $unusedPackages[] = $package;
        }

        return [
            'used'   => $usedPackages,
            'unused' => $unusedPackages
        ];
    }

    private function logCommandInfo(Composer $composer): void
    {
        $requires = [];
        $devRequires = [];

        foreach ($composer->getPackage()->getRequires() as $name => $require) {
            $requires[$name] = $require->getPrettyConstraint();
        }

        foreach ($composer->getPackage()->getDevRequires() as $name => $require) {
            $devRequires[$name] = $require->getPrettyConstraint();
        }

        $this->logger->info('version', ['value' => UnusedPlugin::VERSION]);
        $this->logger->info('requires', $requires);
        $this->logger->info('dev-requires', $devRequires);
        $this->logger->info('autoload', $composer->getPackage()->getAutoload());
        $this->logger->info('dev-autoload', $composer->getPackage()->getDevAutoload());
    }
}
