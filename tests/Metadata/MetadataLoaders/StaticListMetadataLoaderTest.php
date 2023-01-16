<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders;

use Exception;
use ExceptionHandler\Lib\StaticListLoader;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaders\StaticListMetadataLoader;
use PHPUnit\Framework\TestCase;

final class StaticListMetadataLoaderTest extends TestCase
{
    public function testItDelegateSupportingToInnerLoader(): void
    {
        $innerLoader = $this->createMock(StaticListLoader::class);
        $innerLoader->expects($this->once())->method('supports')->willReturn(true);

        $loader = new StaticListMetadataLoader($innerLoader);

        $this->assertTrue($loader->supports(new Exception()));
    }

    public function testItDelegateLoadingToInnerLoader(): void
    {
        $exception = new Exception();
        $metadata = new ExceptionMetadata(1, 'Error Message', $exception);

        $innerLoader = $this->createMock(StaticListLoader::class);
        $innerLoader->expects($this->once())->method('load')->willReturn($metadata);

        $loader = new StaticListMetadataLoader($innerLoader);

        $this->assertSame($metadata, $loader->load($exception));
    }
}
