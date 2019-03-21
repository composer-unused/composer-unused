<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Command;

use Composer\Factory;
use Composer\IO\NullIO;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class UnusedCommandTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function itShouldDoSomething(): void
    {
        $this->markTestIncomplete('Needs some stubbed composer.json with possible errors');

        chdir(__DIR__ . '/../../../');
        $composer = Factory::create(new NullIO(), 'composer.json');
        chdir(__DIR__);

        $command = new UnusedCommand();
        $command->setComposer($composer);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);
    }
}
