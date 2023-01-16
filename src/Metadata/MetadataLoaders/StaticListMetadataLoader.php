<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\MetadataLoaderInterface;
use ExceptionHandler\Lib\StaticListLoader;
use Throwable;

final class StaticListMetadataLoader implements MetadataLoaderInterface
{
    private StaticListLoader $loader;

    public function __construct(StaticListLoader $loader)
    {
        $this->loader = $loader;
    }

    public function supports(Throwable $e): bool
    {
        return $this->loader->supports($e);
    }

    public function load(Throwable $e): ExceptionMetadata
    {
        $metadata = $this->loader->load($e);

        assert($metadata instanceof ExceptionMetadata);

        return $metadata;
    }
}
