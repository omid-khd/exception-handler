<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use Exception;
use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\AttributeTranslationConfigLoader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AttributeTranslationConfigLoaderTest extends TestCase
{
    public function testItDoesNotSupportLoadingForPHPVersionsBelow8(): void
    {
        $loader = new AttributeTranslationConfigLoader();

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
        $loader = new AttributeTranslationConfigLoader();
        $this->assertFalse($loader->supports($exception));
    }

    public function testItSupportThrowableThatContainTranslationConfigAttribute(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);
        } else {
            $loader = new AttributeTranslationConfigLoader();
            $this->assertTrue($loader->supports(new ExceptionWithAttribute('Error')));
        }
    }

    public function testItThrowExceptionIfPHPVersionIsLessThan8(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $loader = new AttributeTranslationConfigLoader();
            $this->expectException(RuntimeException::class);
            $loader->load(new Exception());
        } else {
            $this->assertTrue(true);
        }
    }

    public function testItLoadTranslationConfigByReadingAttribute(): void
    {
        if ($this->phpVersionIsBelow8()) {
            $this->assertTrue(true);

            return;
        }

        $loader = new AttributeTranslationConfigLoader();
        $config = $loader->load(new ExceptionWithAttribute());

        $this->assertEquals('trans_id', $config->id);
    }

    private function phpVersionIsBelow8(): bool
    {
        return PHP_VERSION_ID < 80000;
    }
}
