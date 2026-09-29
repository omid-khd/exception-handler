<?php

declare(strict_types=1);

namespace ExceptionHandler\Http\Translation;

use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;

final class HttpRequestAwarePreferredLocaleProvider implements PreferredLocaleProviderInterface
{
    public function __construct(private readonly HttpRequestProviderInterface $httpRequestProvider)
    {
    }

    public function getPreferredLocale(): ?string
    {
        $headerLine = $this->httpRequestProvider->getHttpRequest()->getHeaderLine('Accept-Language');

        if ($headerLine === '') {
            return null;
        }

        $locales = [];

        foreach (explode(',', $headerLine) as $range) {
            $range = trim($range);

            if ($range === '' || $range === '*') {
                continue;
            }

            [$locale, $quality] = $this->splitQuality($range);

            if ($quality === 0.0) {
                continue;
            }

            $locales[] = [$locale, $quality];
        }

        usort($locales, static fn (array $a, array $b): int => $b[1] <=> $a[1]);

        return $locales === [] ? null : $locales[0][0];
    }

    /**
     * @return array{0: string, 1: float}
     */
    private function splitQuality(string $range): array
    {
        $parts = explode(';', $range, 2);

        if (!isset($parts[1]) || !str_starts_with(strtolower(trim($parts[1])), 'q=')) {
            return [$parts[0], 1.0];
        }

        return [$parts[0], (float) substr(trim($parts[1]), 2)];
    }
}
