<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Lib;

use Exception;
use ExceptionHandler\Exception\FactoryResolutionException;
use ExceptionHandler\Lib\StaticList;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use stdClass;
use Throwable;

final class StaticListTest extends TestCase
{
    public function testItDoesNotSupportThrowableIfItsFQNIsNotGivenInTheList(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([
            RuntimeException::class => static function () {
                // no-op
            },
        ]);

        $this->assertFalse($loader->has(new Exception()));

    }

    public function testItSupportThrowableByItsFQN(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => static function () {
                // no-op
            },
        ]);

        $this->assertTrue($loader->has(new Exception()));
    }

    public function testItSupportThrowableByItsParentClassFQN(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([
            Exception::class => static function () {
                // no-op
            },
        ]);

        $this->assertTrue($loader->has(new RuntimeException()));
    }

    public function testItSupportThrowableByItsInterfaceFQN(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([
            Throwable::class => static function () {
                // no-op
            },
        ]);

        $this->assertTrue($loader->has(new Exception()));
    }

    public function testItThrowExceptionIfGivenLoaderIsNotCallableServiceIdOrArray(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([Exception::class => true]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItLoadBasedOnACallableLoader(): void
    {
        $e = new Exception();

        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([$e::class => static fn () => true]);

        $factory = $loader->get($e);

        $this->assertTrue($factory($e));
    }

    public function testItLoadBasedOnServiceId(): void
    {
        $loaderServiceId = 'loader_service_id';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with($loaderServiceId)->willReturn(true);
        $container->expects($this->once())->method('get')->with($loaderServiceId)->willReturn(static fn () => true);

        $e = new Exception();
        $loader = new StaticList($container);
        $loader->setList([$e::class => $loaderServiceId]);

        $factory = $loader->get(new Exception());

        $this->assertTrue($factory($e));
    }

    public function testItThrowExceptionIfGivenServiceDoesNotContainAMethodNamedInvoke(): void
    {
        $loaderServiceId = 'loader_service_id';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with($loaderServiceId)->willReturn(true);
        $container->expects($this->once())->method('get')->with($loaderServiceId)->willReturn(new stdClass());

        $loader = new StaticList($container);
        $loader->setList([Exception::class => $loaderServiceId]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItLoadBasedOnArray(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with('dummy_loader')->willReturn(true);
        $container->expects($this->once())->method('get')->with('dummy_loader')->willReturn(new class {
            public function load()
            {
                return true;
            }
        });

        $e = new Exception();
        $loader = new StaticList($container);
        $loader->setList([$e::class => ['dummy_loader', 'load']]);

        $factory = $loader->get($e);

        $this->assertTrue($factory($e));
    }

    public function testItLoadBasedOnServiceAtMethodSyntax(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with('loader')->willReturn(true);
        $container->expects($this->once())->method('get')->with('loader')->willReturn(new class {
            public function load(): bool
            {
                return true;
            }
        });

        $e = new Exception();

        $loader = new StaticList($container);
        $loader->setList([$e::class => 'loader@load']);

        $factory = $loader->get($e);

        $this->assertTrue($factory($e));
    }

    public function testItThrowExceptionIfCountOfGivenArrayIsNotEqualTo2(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([Exception::class => ['dummy_loader', 'load', 'extra_item']]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItThrowExceptionIfArrayFirstItemIsNotString(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([Exception::class => [new stdClass(), 'load']]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItThrowExceptionIfArraySecondItemIsNotString(): void
    {
        $loader = new StaticList($this->createMock(ContainerInterface::class));
        $loader->setList([Exception::class => ['dummy_loader', new stdClass()]]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItThrowExceptionIfGivenServiceDoesNotHaveGivenMethod(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(stdClass::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(stdClass::class)->willReturn(new stdClass());

        $loader = new StaticList($container);
        $loader->setList([Exception::class => [stdClass::class, 'load']]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }

    public function testItThrowExceptionIfFactoryDoesNotHasGivenServiceId(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(stdClass::class)->willReturn(false);

        $loader = new StaticList($container);
        $loader->setList([Exception::class => [stdClass::class, 'load']]);

        $this->expectException(FactoryResolutionException::class);

        $loader->get(new Exception());
    }
}
