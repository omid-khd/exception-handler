<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use ReflectionAttribute;
use ReflectionClass;
use Throwable;
use Webmozart\Assert\Assert;

final class AttributeMetadataLoader implements MetadataLoaderInterface
{
    /**
     * @var array<class-string, ThrowableMetadata|null>
     */
    private array $attributeCache = [];

    public function support(Throwable $e): bool
    {
        return $this->getThrowableMetadataAttribute($e) !== null;
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        $attribute = $this->getThrowableMetadataAttribute($e);

        Assert::isInstanceOf($attribute, ThrowableMetadata::class);

        return new ExceptionMetadata($attribute->code, $attribute->message, $e);
    }

    private function getThrowableMetadataAttribute(Throwable $e): ?ThrowableMetadata
    {
        $class = $e::class;

        if (!array_key_exists($class, $this->attributeCache)) {
            $this->attributeCache[$class] = $this->resolveAttribute($class);
        }

        return $this->attributeCache[$class];
    }

    private function resolveAttribute(string $class): ?ThrowableMetadata
    {
        $attributes = (new ReflectionClass($class))->getAttributes(ThrowableMetadata::class);
        $attribute = $attributes[0] ?? null;

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }
}
