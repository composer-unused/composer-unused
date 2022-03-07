<?php

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter;

use ComposerUnused\ComposerUnused\OutputFormatter\DefaultFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\FormatterFactory;
use ComposerUnused\ComposerUnused\OutputFormatter\GithubFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDetector;
use OndraM\CiDetector\Ci\GitHubActions;
use OndraM\CiDetector\CiDetectorInterface;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use PHPUnit\Framework\TestCase;

final class FormatterFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateDefaultFormatterOnNullTypeAndDetectorException(): void
    {
        $detector = $this->createMock(CiDetectorInterface::class);
        $detector->method('detect')->willThrowException(new CiNotDetectedException());

        $factory = new FormatterFactory($detector);
        self::assertInstanceOf(DefaultFormatter::class, $factory->create(null));
    }

    /**
     * @test
     */
    public function itShouldCreateFormatterByCiDetector(): void
    {
        $ciDetector = new TestDetector(GitHubActions::class);

        $factory = new FormatterFactory($ciDetector);
        self::assertInstanceOf(GithubFormatter::class, $factory->create(null));
    }

    /**
     * @test
     */
    public function itShouldCreateGithubFormatterOnGithubType(): void
    {
        $detector = $this->createMock(CiDetectorInterface::class);
        $detector->method('detect')->willThrowException(new CiNotDetectedException());

        $factory = new FormatterFactory($detector);
        self::assertInstanceOf(GithubFormatter::class, $factory->create('github'));
    }

    /**
     * @test
     */
    public function itShouldCreateDefaultFormatterOnDefaultType(): void
    {
        $factory = new FormatterFactory($this->createMock(CiDetectorInterface::class));
        self::assertInstanceOf(DefaultFormatter::class, $factory->create('default'));
    }
}
