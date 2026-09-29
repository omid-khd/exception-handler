<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;
use Webmozart\Assert\Assert;

class ExceptionMetadataLoader
{
    /**
     * @var list<MetadataLoaderInterface>
     */
    private readonly array $metadataLoaders;

    /**
     * @param iterable<MetadataLoaderInterface> $metadataLoaders
     */
    public function __construct(iterable $metadataLoaders = [])
    {
        $loaders = [];

        foreach ($metadataLoaders as $metadataLoader) {
            Assert::isInstanceOf($metadataLoader, MetadataLoaderInterface::class);
            $loaders[] = $metadataLoader;
        }

        $this->metadataLoaders = $loaders;
    }

    public function loadMetadata(Throwable $e): ExceptionMetadata
    {
        foreach ($this->metadataLoaders as $metadataLoader) {
            if ($metadataLoader->support($e)) {
                return $metadataLoader->load($e);
            }
        }

        return new ExceptionMetadata(500, 'Internal Server Error', $e);
    }
}
