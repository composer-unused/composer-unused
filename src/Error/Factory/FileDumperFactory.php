<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Factory;

use Composer\Composer;
use DateTime;
use Icanhazstring\Composer\Unused\Error\FileDumper;
use Psr\Container\ContainerInterface;

class FileDumperFactory
{
    /**
     * @param ContainerInterface $container
     * @return FileDumper
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container): FileDumper
    {
        $dumpFileName = 'composer-unused-dump-' . (new DateTime())->format('YmdHis');

        return new FileDumper($dumpFileName, $container->get(Composer::class));
    }
}
