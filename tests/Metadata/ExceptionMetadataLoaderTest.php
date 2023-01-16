<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata;

use Exception;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\ExceptionMetadataLoader;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use PHPUnit\Framework\TestCase;

final class ExceptionMetadataLoaderTest extends TestCase
{
    public function testItReturnInternalServerErrorMetadataByDefault(): void
    {
        $loader = new ExceptionMetadataLoader([]);

        $metadata = $loader->loadMetadata(new Exception());
        $this->assertEquals(500, $metadata->getCode());
        $this->assertEquals('Internal Server Error', $metadata->getMessage());
    }

    public function testItReturnInternalServerErrorMetadataIfNoneOfLoadersSupportGivenException(): void
    {
        $metadataLoader = $this->createMock(MetadataLoaderInterface::class);
        $metadataLoader->expects($this->once())->method('supports')->willReturn(false);

        $loader = new ExceptionMetadataLoader([$metadataLoader]);

        $metadata = $loader->loadMetadata(new Exception());
        $this->assertEquals(500, $metadata->getCode());
        $this->assertEquals('Internal Server Error', $metadata->getMessage());
    }

    public function testItDelegateLoadingMetadataToMetadataLoaders(): void
    {
        $exception = new Exception();
        $metadata = new ExceptionMetadata(1, 'Error Message', $exception);

        $metadataLoader = $this->createMock(MetadataLoaderInterface::class);
        $metadataLoader->expects($this->once())->method('supports')->willReturn(true);
        $metadataLoader->expects($this->once())->method('load')->willReturn($metadata);

        $loader = new ExceptionMetadataLoader([$metadataLoader]);

        $this->assertSame($metadata, $loader->loadMetadata($exception));
    }
}
