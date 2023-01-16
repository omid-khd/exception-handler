<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use Exception;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaders\MetadataAware\MetadataAwareExceptionInterface;
use ExceptionHandler\Metadata\MetadataLoaders\MetadataAware\MetadataAwareExceptionMetadataLoader;
use PHPUnit\Framework\TestCase;

final class MetadataAwareExceptionMetadataLoaderTest extends TestCase
{
    public function testItSupportsInstanceOfMetadataAwareExceptionInterface(): void
    {
        $loader = new MetadataAwareExceptionMetadataLoader();

        $this->assertFalse($loader->supports(new Exception()));

        $metadataAwareException = new class extends Exception implements MetadataAwareExceptionInterface {
            public function getMetadata(): ExceptionMetadata
            {
            }
        };

        $this->assertTrue($loader->supports($metadataAwareException));
    }

    public function testItLoadMetadataByDelegatingToException(): void
    {
        $loader = new MetadataAwareExceptionMetadataLoader();

        $metadataAwareException = new class extends Exception implements MetadataAwareExceptionInterface {
            public function getMetadata(): ExceptionMetadata
            {
                return new ExceptionMetadata(1, 'Error Message', $this);
            }
        };

        $metadata = $loader->load($metadataAwareException);

        $this->assertEquals(1, $metadata->getCode());
        $this->assertEquals('Error Message', $metadata->getMessage());
        $this->assertSame($metadataAwareException, $metadata->getThrowable());
    }
}
