<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Http\Translation;

use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Http\Translation\HttpRequestAwarePreferredLocaleProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;

final class HttpRequestAwarePreferredLocaleProviderTest extends TestCase
{
    public function testItReturnNullIfAcceptLanguageHeaderContainsNoValue(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('Accept-Language')->willReturn('');

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertNull($provider->getPreferredLocale());
    }

    public function testItReturnPreferredLanguageSpecifiedByRequestAcceptHeader(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('Accept-Language')->willReturn('en_EN');

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertEquals('en_EN', $provider->getPreferredLocale());
    }

    public function testItReturnsFirstLanguageWhenAllHaveDefaultQuality(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('Accept-Language')->willReturn('en-US,en;q=0.9,fr;q=0.8');

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertEquals('en-US', $provider->getPreferredLocale());
    }

    public function testItReturnsLanguageWithHighestQuality(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('Accept-Language')->willReturn('fr;q=0.9,en');

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertEquals('en', $provider->getPreferredLocale());
    }

    public function testItReturnsNullIfHeaderOnlyContainsWildcard(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('Accept-Language')->willReturn('*');

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertNull($provider->getPreferredLocale());
    }
}
