<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class MetadataAwareExceptionMetadataLoader implements MetadataLoaderInterface
{
    public function support(Throwable $e): bool
    {
        return $e instanceof MetadataAwareExceptionInterface;
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        Assert::isInstanceOf($e, MetadataAwareExceptionInterface::class);

        return $e->getMetadata();
    }
}
