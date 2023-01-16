<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Lib;

use Exception;
use ExceptionHandler\Exception\FactoryResolutionException;
use ExceptionHandler\Lib\StaticListLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use stdClass;
use Throwable;

final class StaticListLoaderTest extends TestCase
{
    public function testItDoesNotSupportThrowableIfItsFQNIsNotGivenInTheList(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            RuntimeException::class => static function () {
            },
        ]);

        $this->assertFalse($loader->supports(new Exception()));

    }

    public function testItSupportThrowableByItsFQN(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new Exception()));
    }

    public function testItSupportThrowableByItsParentClassFQN(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new RuntimeException()));
    }

    public function testItSupportThrowableByItsInterfaceFQN(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Throwable::class => static function () {
            },
        ]);

        $this->assertTrue($loader->supports(new Exception()));
    }

    public function testItThrowExceptionIfGivenLoaderIsNotCallableServiceIdOrArray(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => true,
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItLoadBasedOnACallableLoader(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => static function () {
                return true;
            },
        ]);

        $this->assertTrue($loader->load(new Exception()));
    }

    public function testItLoadBasedOnServiceId(): void
    {
        $loaderServiceId = 'loader_service_id';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with($loaderServiceId)->willReturn(true);
        $container->expects($this->once())->method('get')->with($loaderServiceId)->willReturn(function () {
            return true;
        });

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => $loaderServiceId,
        ]);

        $this->assertTrue($loader->load(new Exception()));
    }

    public function testItThrowExceptionIfGivenServiceDoesNotContainAMethodNamedInvoke(): void
    {
        $loaderServiceId = 'loader_service_id';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with($loaderServiceId)->willReturn(true);
        $container->expects($this->once())->method('get')->with($loaderServiceId)->willReturn(new stdClass());

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => $loaderServiceId,
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItLoadBasedOnArray(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with('dummy_loader')->willReturn(true);
        $container->expects($this->once())->method('get')->with('dummy_loader')->willReturn(new DummyLoader());

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => ['dummy_loader', 'load'],
        ]);

        $this->assertTrue($loader->load(new Exception()));
    }

    public function testItLoadBasedOnServiceAtMethodSyntax(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(DummyLoader::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(DummyLoader::class)->willReturn(new DummyLoader());

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => sprintf('%s@load', DummyLoader::class),
        ]);

        $this->assertTrue($loader->load(new Exception()));
    }

    public function testItThrowExceptionIfCountOfGivenArrayIsNotEqualTo2(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => ['dummy_loader', 'load', 'extra_item'],
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItThrowExceptionIfArrayFirstItemIsNotString(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => [new stdClass(), 'load'],
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItThrowExceptionIfArraySecondItemIsNotString(): void
    {
        $loader = new StaticListLoader($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => ['dummy_loader', new stdClass()],
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItThrowExceptionIfGivenServiceDoesNotHaveGivenMethod(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(stdClass::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(stdClass::class)->willReturn(new stdClass());

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => [stdClass::class, 'load'],
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }

    public function testItThrowExceptionIfFactoryDoesNotHasGivenServiceId(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(stdClass::class)->willReturn(false);

        $loader = new StaticListLoader($container);
        $loader->setList([
            Exception::class => [stdClass::class, 'load'],
        ]);

        $this->expectException(FactoryResolutionException::class);

        $loader->load(new Exception());
    }
}

class DummyLoader
{
    public function load()
    {
        return true;
    }
}