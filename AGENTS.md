# AGENTS.md

PHP 8.3+ library (`omid-khd/exception-handler`), namespace `ExceptionHandler\` -> `src/`, tests in `tests/` (namespace `Tests\ExceptionHandler\`, mirroring src). No lint, formatter, or typecheck tooling — only PHPUnit.

## Commands

- Install: `composer install`
- Full suite: `vendor/bin/phpunit` (CI runs `vendor/bin/phpunit tests`)
- Single file: `vendor/bin/phpunit tests/MiddlewareTest.php`
- Single test: `vendor/bin/phpunit --filter testName tests/SomeTest.php`

## PHPUnit quirks

`phpunit.xml` sets `failOnRisky` and `failOnWarning` — tests that lack assertions, emit output, or trigger PHP warnings fail the suite. Keep tests assertion-only and warning-free.

CI matrix: PHP 8.3–8.5, lowest + highest composer dependencies.

## Architecture notes

- Middleware chain: an item earlier in the `$middlewares` array is outermost (runs first, wraps the rest). `handle()` returns the result of the first middleware; the metadata loader is always appended last.
- A middleware that needs the `ExceptionMetadata` must call `$next($e)` first (e.g. `TranslationMiddleware`, `HttpResponseMiddleware`). Getting this order wrong is the most common bug.
- `ExceptionMetadata` and `TranslationConfig` are immutable value objects; do not add setters.
- Static list factories (`StaticList`) resolve lazily from a PSR-11 container; load failure throws `FactoryResolutionException` (src/Exception/FactoryResolutionException.php).
