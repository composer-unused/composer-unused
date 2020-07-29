<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Parser\PHP\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\PHPUsageParser;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PHPUsageParserFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array<string, mixed>|null $options
     * @return PHPUsageParser
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): PHPUsageParser
    {
        return new PHPUsageParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $container->get(NodeVisitor::class),
            $container->get(ErrorHandlerInterface::class),
            $container->get(LoggerInterface::class),
            new Result(),
            $options['excludes'] ?? []
        );
    }
}
