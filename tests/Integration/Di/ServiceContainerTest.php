<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Integration\Di;

use ComposerUnused\ComposerUnused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function gettype;

class ServiceContainerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldLoadServiceContainer(): void
    {
        /** @var ServiceContainer $container */
        $container = require __DIR__ . '/../../../config/container.php';
        $services = require __DIR__ . '/../../../config/service_manager.php';

        foreach ($services['factories'] as $type => $factory) {
            self::assertInstanceOf($type, $container->get($type));
        }
    }
}
