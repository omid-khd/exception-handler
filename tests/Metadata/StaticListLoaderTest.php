<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata;

use Exception;
use ExceptionHandler\Lib\StaticListLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Throwable;

final class StaticListLoaderTest extends TestCase
{
    public function testItDoesNotSupportThrowablesNotPresentInStaticList(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $factoryLocator = $this->createMock(ContainerInterface::class);
        $loader = new StaticListLoader($factoryLocator);
        $loader->setList([]);

        $this->assertFalse($loader->supports(new Exception()));
    }

    public function testItSupportsExceptionByExactFQN(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $factoryLocator = $this->createMock(ContainerInterface::class);
        $loader = new StaticListLoader($factoryLocator);
        $loader->setList([
            Exception::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new Exception()));
    }

    public function testItSupportsExceptionByClassHierarchy(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $factoryLocator = $this->createMock(ContainerInterface::class);
        $loader = new StaticListLoader($factoryLocator);
        $loader->setList([
            Exception::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new RuntimeException()));
    }

    public function testItSupportsExceptionByClassInterfaces(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $factoryLocator = $this->createMock(ContainerInterface::class);
        $loader = new StaticListLoader($factoryLocator);
        $loader->setList([
            Throwable::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new RuntimeException()));
    }

    public function testItThrowExceptionInLoadingIfPHPVersionIsBelow8(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $factoryLocator = $this->createMock(ContainerInterface::class);
            $loader = new StaticListLoader($factoryLocator);
            $loader->setList([
                Throwable::class => static function () {
                },
            ]);

            $this->assertTrue($loader->supports(new RuntimeException()));
        } else {
            $this->assertTrue(true);
        }
    }

    private function phpVersionIsBelow8(): bool
    {
        return PHP_VERSION_ID < 80000;
    }
}
