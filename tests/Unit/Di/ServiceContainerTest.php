<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Di;

use Icanhazstring\Composer\Unused\Di\Exception\ServiceNotFoundException;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

class ServiceContainerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldRaiseExceptionWhenSerivceNotResolved(): void
    {
        $container = new ServiceContainer();

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage('Could not resolve test to a factory.');
        $container->get('test');
    }

    /**
     * @test
     */
    public function itShouldResolveService(): void
    {
        $container = new ServiceContainer([
            'factories' => [
                'test' => static function () {
                    return new stdClass();
                }
            ]
        ]);

        $this->assertInstanceOf(stdClass::class, $container->get('test'));
    }

    /**
     * @test
     */
    public function itShouldCacheServicesUsingGet(): void
    {
        $container = new ServiceContainer([
            'factories' => [
                'test' => static function () {
                    return new stdClass();
                }
            ]
        ]);

        $this->assertSame($container->get('test'), $container->get('test'));
    }

    /**
     * @test
     */
    public function itShouldResolveServiceUsingBuild(): void
    {
        $refObject = new stdClass();

        $container = new ServiceContainer([
            'factories' => [
                'test' => static function ($container, $requestedName, $options) use ($refObject) {
                    $refObject->{$options['flagName']} = $options['flagValue'];

                    return $refObject;
                }
            ]
        ]);

        $object = $container->build('test', ['flagName' => 'flag', 'flagValue' => true]);
        self::assertTrue($object->flag);
    }

    /**
     * @test
     */
    public function itShouldNotCacheServicesUsingBuild(): void
    {
        $container = new ServiceContainer([
            'factories' => [
                'test' => static function () {
                    return new stdClass();
                }
            ]
        ]);

        $this->assertNotSame($container->build('test'), $container->build('test'));
    }
}
