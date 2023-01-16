<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Lib;

use ExceptionHandler\Lib\StaticList;
use ExceptionHandler\Lib\StaticListLoaderConfigurator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StaticListLoaderConfiguratorTest extends TestCase
{
    public function testItThrowExceptionIfGivenFileDoesNotExist(): void
    {
        $configurator = new StaticListLoaderConfigurator('list.php');

        $this->expectException(InvalidArgumentException::class);

        $configurator->configure($this->createMock(StaticList::class));
    }

    public function testItThrowExceptionIfGivenFileIsNotAPHPFile(): void
    {
        $file = __DIR__ . '/list.txt';

        try {
            touch($file);

            $configurator = new StaticListLoaderConfigurator($file);

            $this->expectException(InvalidArgumentException::class);

            $configurator->configure($this->createMock(StaticList::class));
        } finally {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testItConfigureByArray(): void
    {
        $list = [];
        $configurator = new StaticListLoaderConfigurator($list);

        $loader = $this->createMock(StaticList::class);
        $loader->expects($this->once())->method('setList')->with($list);

        $configurator->configure($loader);
    }

    public function testItConfigureByFile(): void
    {
        $list = [];
        $file = __DIR__ . '/list.php';

        try {
            file_put_contents($file, sprintf('<?php return %s;', var_export($list, true)));

            $configurator = new StaticListLoaderConfigurator($list);

            $loader = $this->createMock(StaticList::class);
            $loader->expects($this->once())->method('setList')->with($list);

            $configurator->configure($loader);
        } finally {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
