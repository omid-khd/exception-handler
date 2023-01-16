<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

use ExceptionHandler\Metadata\ExceptionMetadata;
use Throwable;
use Webmozart\Assert\Assert;

final readonly class TranslationMiddleware
{
    public function __construct(
        private TranslationConfigLoader $configLoader,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(Throwable $e, callable $next): ExceptionMetadata
    {
        $metadata = $next($e);

        Assert::isInstanceOf($metadata, ExceptionMetadata::class);

        $config = $this->configLoader->load($e);

        if ($config instanceof TranslationConfig) {
            $message = $this->translator->trans($config->id, $config->parameters, $config->domain, $config->locale);
            $metadata = new ExceptionMetadata($metadata->getCode(), $message, $metadata->getThrowable());
        }

        return $metadata;
    }
}
