<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use Exception;
use ExceptionHandler\Metadata\MetadataLoaders\Attribute\AttributeMetadataLoader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AttributeMetadataLoaderTest extends TestCase
{
    public function testItDoesNotSupportLoadingForPHPVersionsBelow8(): void
    {
        $loader = new AttributeMetadataLoader();
        $exception = new Exception('Error');

        if ($this->phpVersionIsBelow8()) {
            $this->assertFalse($loader->supports($exception));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testItDontSupportThrowableThatDoesNotContainTranslationConfigAttribute(): void
    {
        $exception = new Exception('Error');
        $loader = new AttributeMetadataLoader();

        $this->assertFalse($loader->supports($exception));
    }

    public function testItSupportThrowableThatContainTranslationConfigAttribute(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);
        } else {
            $loader = new AttributeMetadataLoader();
            $this->assertTrue($loader->supports(new ExceptionWithAttribute('Error')));
        }
    }

    public function testItThrowExceptionIfPHPVersionIsLessThan8(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $loader = new AttributeMetadataLoader();
            $this->expectException(RuntimeException::class);
            $loader->load(new Exception());
        } else {
            $this->assertTrue(true);
        }
    }

    public function testItLoadExceptionMetadataByReadingAttribute(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $loader = new AttributeMetadataLoader();
        $metadata = $loader->load(new ExceptionWithAttribute());

        $this->assertEquals('Error Message', $metadata->getMessage());
        $this->assertEquals(500, $metadata->getCode());
    }

    private function phpVersionIsBelow8(): bool
    {
        return PHP_VERSION_ID < 80000;
    }
}
