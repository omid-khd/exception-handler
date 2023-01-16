<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders;

use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;
use ExceptionHandler\Translation\TranslationConfig;
use Throwable;

final readonly class PreferredLocaleDecorator implements TranslationConfigLoaderInterface
{
    private const string LOCALE_EN = 'en';

    public function __construct(
        private TranslationConfigLoaderInterface $translationConfigLoader,
        private PreferredLocaleProviderInterface $preferredLocaleProvider,
    ) {
    }

    public function support(Throwable $e): bool
    {
        return $this->translationConfigLoader->support($e);
    }

    public function load(Throwable $e): TranslationConfig
    {
        $config = $this->translationConfigLoader->load($e);
        $config->locale = $this->preferredLocaleProvider->getPreferredLocale() ?? self::LOCALE_EN;

        return $config;
    }
}
