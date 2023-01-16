<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

use ExceptionHandler\Metadata\ExceptionMetadata;
use Throwable;

final class TranslationMiddleware
{
    private TranslationConfigLoader $configLoader;
    private TranslatorInterface $translator;

    public function __construct(TranslationConfigLoader $configLoader, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->configLoader = $configLoader;
    }

    public function __invoke(Throwable $e, callable $next): ExceptionMetadata
    {
        $metadata = $next($e);

        assert($metadata instanceof ExceptionMetadata);

        $config = $this->configLoader->load($e);

        if ($config instanceof TranslationConfig) {
            $metadata = new ExceptionMetadata($metadata->getCode(), $this->trans($config), $metadata->getThrowable());
        }

        return $metadata;
    }

    private function trans(TranslationConfig $config): string
    {
        return $this->translator->trans($config->id, $config->parameters, $config->domain, $config->locale);
    }
}
