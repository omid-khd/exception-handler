<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders;

use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;
use ExceptionHandler\Translation\TranslationConfig;
use Throwable;

final class PreferredLocaleDecorator implements TranslationConfigLoaderInterface
{
    private const LOCALE_EN = 'en';

    private TranslationConfigLoaderInterface $translationConfigLoader;
    private PreferredLocaleProviderInterface $preferredLocaleProvider;

    public function __construct(
        TranslationConfigLoaderInterface $translationConfigLoader,
        PreferredLocaleProviderInterface $preferredLocaleProvider
    ) {
        $this->translationConfigLoader = $translationConfigLoader;
        $this->preferredLocaleProvider = $preferredLocaleProvider;
    }

    public function supports(Throwable $e): bool
    {
        return $this->translationConfigLoader->supports($e);
    }

    public function load(Throwable $e): TranslationConfig
    {
        $config = $this->translationConfigLoader->load($e);
        $config->locale = $this->preferredLocaleProvider->getPreferredLocale() ?? self::LOCALE_EN;

        return $config;
    }
}
