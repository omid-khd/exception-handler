<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use ExceptionHandler\Lib\StaticList;
use Throwable;
use Webmozart\Assert\Assert;

final class StaticListMetadataLoader implements MetadataLoaderInterface
{
    public function __construct(private readonly StaticList $loader)
    {
    }

    public function support(Throwable $e): bool
    {
        return $this->loader->has($e);
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        $factory = $this->loader->get($e);
        $metadata = $factory($e);

        Assert::isInstanceOf($metadata, ExceptionMetadata::class);

        return $metadata;
    }
}
