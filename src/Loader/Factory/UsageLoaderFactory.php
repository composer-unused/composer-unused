<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Parser\PHP\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\PHPUsageParser;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UsageLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface&ServiceContainer $container
     * @param string $requestedName
     * @param array<string, mixed>|null $options
     * @return UsageLoader
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): UsageLoader
    {
        return new UsageLoader(
            [
                $container->build(PHPUsageParser::class, $options),
            ],
            new Result()
        );
    }
}
