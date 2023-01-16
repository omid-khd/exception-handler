<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use Throwable;

final class MetadataAwareExceptionMetadataLoader implements MetadataLoaderInterface
{
    public function supports(Throwable $e): bool
    {
        return $e instanceof MetadataAwareExceptionInterface;
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        assert($e instanceof MetadataAwareExceptionInterface);

        return $e->getMetadata();
    }
}
