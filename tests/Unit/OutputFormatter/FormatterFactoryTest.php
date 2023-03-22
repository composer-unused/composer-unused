<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter;

use ComposerUnused\ComposerUnused\OutputFormatter\DefaultFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\FormatterFactory;
use ComposerUnused\ComposerUnused\OutputFormatter\GithubFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\GitlabFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\JsonFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\JUnitFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDetector;
use OndraM\CiDetector\Ci\GitHubActions;
use OndraM\CiDetector\Ci\GitLab;
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
    public function itShouldCreateGithubFormatterByCiDetector(): void
    {
        $ciDetector = new TestDetector(GitHubActions::class);

        $factory = new FormatterFactory($ciDetector);
        self::assertInstanceOf(GithubFormatter::class, $factory->create(null));
    }

    /**
     * @test
     */
    public function itShouldCreateGitlabFormatterByCiDetector(): void
    {
        $ciDetector = new TestDetector(GitLab::class);

        $factory = new FormatterFactory($ciDetector);
        self::assertInstanceOf(GitlabFormatter::class, $factory->create(null));
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
    public function itShouldCreateGitlabFormatterOnGitlabType(): void
    {
        $detector = $this->createMock(CiDetectorInterface::class);
        $detector->method('detect')->willThrowException(new CiNotDetectedException());

        $factory = new FormatterFactory($detector);
        self::assertInstanceOf(GitlabFormatter::class, $factory->create('gitlab'));
    }

    /**
     * @test
     */
    public function itShouldCreateDefaultFormatterOnDefaultType(): void
    {
        $factory = new FormatterFactory($this->createMock(CiDetectorInterface::class));
        self::assertInstanceOf(DefaultFormatter::class, $factory->create('default'));
    }

    /**
     * @test
     */
    public function itShouldCreateJsonFormatterOnJsonType(): void
    {
        $factory = new FormatterFactory($this->createMock(CiDetectorInterface::class));
        self::assertInstanceOf(JsonFormatter::class, $factory->create('json'));
    }

    /**
     * @test
     */
    public function itShouldCreateJUnitFormatterOnJUnitType(): void
    {
        $factory = new FormatterFactory($this->createMock(CiDetectorInterface::class));
        self::assertInstanceOf(JUnitFormatter::class, $factory->create('junit'));
    }
}
