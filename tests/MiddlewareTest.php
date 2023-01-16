<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler;

use ExceptionHandler\Middleware;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MiddlewareTest extends TestCase
{
    public function testLastChainReturnsSubject(): void
    {
        $subject = new stdClass();
        $middleware = new Middleware();

        $this->assertSame($subject, $middleware->handle($subject));
    }

    public function testMiddlewareChainExecution(): void
    {
        $subject = new stdClass();
        $executionOrder = [];

        $middleware1 = static function ($subject, $next) use (&$executionOrder) {
            $executionOrder[] = 'middleware1_before';
            $result = $next($subject);
            $executionOrder[] = 'middleware1_after';

            return $result;
        };

        $middleware2 = static function ($subject, $next) use (&$executionOrder) {
            $executionOrder[] = 'middleware2_before';
            $result = $next($subject);
            $executionOrder[] = 'middleware2_after';

            return $result;
        };

        $middleware = new Middleware([$middleware1, $middleware2]);
        self::assertSame($subject, $middleware->handle($subject));

        self::assertSame([
            'middleware1_before',
            'middleware2_before',
            'middleware2_after',
            'middleware1_after',
        ], $executionOrder);
    }
}
