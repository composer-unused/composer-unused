<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Parser\ParserInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UsageLoader implements LoaderInterface
{
    use ProgressBarTrait;

    /** @var ParserInterface[] */
    private $parsers;
    /** @var ResultInterface */
    private $loaderResult;

    /**
     * @param array<ParserInterface> $parsers
     */
    public function __construct(array $parsers, ResultInterface $loaderResult)
    {
        $this->parsers = $parsers;
        $this->loaderResult = $loaderResult;
    }

    /**
     * @param Composer $composer
     * @param SymfonyStyle $io
     * @return ResultInterface
     */
    public function load(Composer $composer, SymfonyStyle $io): ResultInterface
    {
        foreach ($this->parsers as $parser) {
            $parser->toggleProgress($this->noProgress);

            $this->loaderResult = $this->loaderResult->merge(
                $parser->scan(
                    dirname($composer->getConfig()->getConfigSource()->getName()),
                    $io
                )
            );
        }

        return $this->loaderResult;
    }
}
