<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use ReflectionAttribute;
use ReflectionObject;
use Throwable;
use Webmozart\Assert\Assert;

final class AttributeMetadataLoader implements MetadataLoaderInterface
{
    public function support(Throwable $e): bool
    {
        return $this->getThrowableMetadataAttribute($e) instanceof ThrowableMetadata;
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        $attribute = $this->getThrowableMetadataAttribute($e);

        Assert::isInstanceOf($attribute, ThrowableMetadata::class);

        return new ExceptionMetadata($attribute->code, $attribute->message, $e);
    }

    private function getThrowableMetadataAttribute(Throwable $e): ?ThrowableMetadata
    {
        $refObject = new ReflectionObject($e);
        $attributes = $refObject->getAttributes(ThrowableMetadata::class);
        $attribute = array_shift($attributes);

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }
}
