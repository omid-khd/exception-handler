<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use ExceptionHandler\Metadata\MetadataLoaders\MetadataAware\MetadataAwareExceptionInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;

final class MetadataAwareExceptionInterfaceTest extends TestCase
{
    public function testTheInterfaceExtendsThrowable(): void
    {
        $this->assertTrue((new ReflectionClass(MetadataAwareExceptionInterface::class))->isSubclassOf(Throwable::class));
    }
}
