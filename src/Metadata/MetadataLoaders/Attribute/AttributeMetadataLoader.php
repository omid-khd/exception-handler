<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use ReflectionAttribute;
use ReflectionObject;
use RuntimeException;
use Throwable;

final class AttributeMetadataLoader implements MetadataLoaderInterface
{

    public function supports(Throwable $e): bool
    {
        if ($this->phpVersionIsBelow8()) {
            return false;
        }

        return $this->getThrowableMetadataAttribute($e) instanceof ThrowableMetadata;
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        if ($this->phpVersionIsBelow8()) {
            throw new RuntimeException(sprintf('Minimum required version for using %s is 8.0.0', __CLASS__));
        }

        $attribute = $this->getThrowableMetadataAttribute($e);

        assert($attribute instanceof ThrowableMetadata);

        return new ExceptionMetadata($attribute->code, $attribute->message, $e);
    }

    private function getThrowableMetadataAttribute(Throwable $e): ?ThrowableMetadata
    {
        $refObject = new ReflectionObject($e);
        $attributes = $refObject->getAttributes(ThrowableMetadata::class);
        $attribute = array_shift($attributes);

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }

    /**
     * @return bool
     */
    private function phpVersionIsBelow8(): bool
    {
        return PHP_VERSION_ID < 80000;
    }
}
