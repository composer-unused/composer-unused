<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log\Factory;

use DateTime;
use Exception;
use Icanhazstring\Composer\Unused\Log\FileHandler;
use Psr\Container\ContainerInterface;

class FileHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return FileHandler
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): FileHandler
    {
        $path = 'composer-unused-dump-' . (new DateTime())->format('YmdHis');

        return new FileHandler($path);
    }
}
