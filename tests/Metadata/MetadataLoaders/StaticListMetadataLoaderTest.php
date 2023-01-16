<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders;

use Exception;
use ExceptionHandler\Lib\StaticList;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaders\StaticListMetadataLoader;
use PHPUnit\Framework\TestCase;

final class StaticListMetadataLoaderTest extends TestCase
{
    public function testItDelegateSupportingToInnerLoader(): void
    {
        $innerLoader = $this->createMock(StaticList::class);
        $innerLoader->expects($this->once())->method('has')->willReturn(true);

        $loader = new StaticListMetadataLoader($innerLoader);

        $this->assertTrue($loader->support(new Exception()));
    }

    public function testItDelegateLoadingToInnerLoader(): void
    {
        $exception = new Exception();
        $metadata = new ExceptionMetadata(1, 'Error Message', $exception);
        $factory = static fn (): ExceptionMetadata => $metadata;

        $innerLoader = $this->createMock(StaticList::class);
        $innerLoader->expects($this->once())->method('get')->willReturn($factory);

        $loader = new StaticListMetadataLoader($innerLoader);

        $this->assertSame($metadata, $loader->load($exception));
    }
}
