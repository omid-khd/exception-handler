# Exception Handler

A flexible and extensible exception handler for PHP applications.

## Requirements

- PHP 8.3 or higher.

## Installation

This package can be installed via Composer:

```bash
composer require omid-khd/exception-handler
```

## Usage

The core of this package is the `ExceptionHandler` class. It takes an
`ExceptionMetadataLoader` and an array of middlewares in its constructor. The
`handle` method is responsible for processing the exception.

### Basic Setup

Here's a basic example of how to set up the `ExceptionHandler`:

```php
use ExceptionHandler\ExceptionHandler;
use ExceptionHandler\Metadata\ExceptionMetadataLoader;

// Create a metadata loader (we'll cover this in more detail later)
$metadataLoader = new ExceptionMetadataLoader(/* ... */);

// Create the exception handler
$exceptionHandler = new ExceptionHandler($metadataLoader);

// Handle an exception
try {
    // ... your code that might throw an exception
} catch (Throwable $e) {
    $metadata = $exceptionHandler->handle($e);
    // ... do something with the metadata
}
```

### Middlewares

The exception handling process is built around a middleware system. You can add
custom middlewares to the `ExceptionHandler` to add custom logic to the handling
process. A middleware is a callable that receives the subject (the exception in
the initial call) and the next middleware in the chain:

```php
use Psr\Log\LoggerInterface;

$loggingMiddleware = function (Throwable $e, callable $next) use ($logger) {
    $logger->error($e->getMessage());

    return $next($e);
};

$exceptionHandler = new ExceptionHandler($metadataLoader, [$loggingMiddleware]);
```

The `ExceptionHandler` adds its own middleware at the end of the chain, which
calls the `ExceptionMetadataLoader`. That is why a middleware that wants to
inspect the metadata must call `$next($e)` first. The result of the `handle`
method is the result of the first middleware in the chain.

### Exception Metadata

The `ExceptionHandler` uses metadata to get information about an exception, such
as the HTTP status code and a user-friendly message. `ExceptionMetadata` is an
immutable value object built from an HTTP status code, a message and the
original throwable:

```php
use ExceptionHandler\Metadata\ExceptionMetadata;

$metadata = new ExceptionMetadata(404, 'Not Found', $e);

$metadata->getCode();      // 404
$metadata->getMessage();   // 'Not Found'
$metadata->getThrowable(); // $e
```

The `ExceptionMetadataLoader` is responsible for loading this metadata. It
iterates over a collection of `MetadataLoaderInterface` implementations until it
finds one that supports the given exception. When none of them supports the
exception, it returns a `500 Internal Server Error` metadata.

This package provides three ways to configure exception metadata:

1.  **`MetadataAwareExceptionMetadataLoader`**: This loader works with exceptions
    that implement the `MetadataAwareExceptionInterface`.

    ```php
    use ExceptionHandler\Metadata\ExceptionMetadata;
    use ExceptionHandler\Metadata\MetadataLoaders\MetadataAware\MetadataAwareExceptionInterface;

    class MyException extends Exception implements MetadataAwareExceptionInterface
    {
        public function getMetadata(): ExceptionMetadata
        {
            return new ExceptionMetadata(403, 'Forbidden', $this);
        }
    }
    ```

2.  **`AttributeMetadataLoader`**: This loader uses PHP 8 attributes to define
    metadata on the exception class itself.

    ```php
    use ExceptionHandler\Metadata\MetadataLoaders\Attribute\ThrowableMetadata;

    #[ThrowableMetadata(code: 400, message: 'Bad Request')]
    class MyException extends Exception
    {
    }
    ```

3.  **`StaticListMetadataLoader`**: This loader uses a static list to map
    exception classes to metadata factories. See
    [Static Lists](#static-lists) for more details.

    ```php
    use ExceptionHandler\Metadata\ExceptionMetadata;
    use ExceptionHandler\Metadata\MetadataLoaders\StaticListMetadataLoader;

    $list = new StaticList($container);
    $list->setList([
        MyException::class => static fn (MyException $e): ExceptionMetadata => new ExceptionMetadata(404, 'Not Found', $e),
    ]);

    $metadataLoader = new StaticListMetadataLoader($list);
    ```

You can combine multiple metadata loaders by passing them as an array to the
`ExceptionMetadataLoader` constructor. The first loader that supports the
exception wins:

```php
$metadataLoader = new ExceptionMetadataLoader([
    new MetadataAwareExceptionMetadataLoader(),
    new AttributeMetadataLoader(),
    new StaticListMetadataLoader($list),
]);
```

## Static Lists

`StaticList` maps exception class names to factories. It is shared by the
`StaticListMetadataLoader` and the `StaticListTranslationConfigLoader`, and it
resolves factories out of a PSR-11 container:

```php
use ExceptionHandler\Lib\StaticList;

$list = new StaticList($container); // $container is a Psr\Container\ContainerInterface
$list->setList([
    MyException::class => static fn (MyException $e): ExceptionMetadata => new ExceptionMetadata(404, 'Not Found', $e),
]);
```

A factory can be given in any of the following forms:

```php
$list->setList([
    // 1. A callable
    MyException::class => static fn (MyException $e) => /* ... */,

    // 2. A service id: the container must return an invokable object
    MyException::class => 'my_exception_metadata_factory',

    // 3. A service id and a method
    MyException::class => 'my_exception_metadata_factory@create',

    // 4. A service id and a method, as an array
    MyException::class => ['my_exception_metadata_factory', 'create'],
]);
```

The `StaticListLoaderConfigurator` is a small helper that configures a
`StaticList` from an array, or from a PHP file that returns the array:

```php
use ExceptionHandler\Lib\StaticList;
use ExceptionHandler\Lib\StaticListLoaderConfigurator;

$configurator = new StaticListLoaderConfigurator(__DIR__ . '/exceptions.php');
$configurator->configure($list);
```

### Exception Hierarchy

Both `StaticListMetadataLoader` and `StaticListTranslationConfigLoader`
intelligently handle exception hierarchies. When looking for a factory, they
will traverse up the exception's class chain, including parent classes and
implemented interfaces. This is useful for setting up metadata and translations
that apply to a single exception class or to a related group of exceptions.

## Web Application Integration

This package is designed to be integrated into a web application. The
`HttpResponseMiddleware` is a middleware that can be added to the
`ExceptionHandler` to transform the `ExceptionMetadata` into an HTTP response.

### `HttpResponseMiddleware`

The `HttpResponseMiddleware` takes an `HttpRequestProviderInterface` and a
`ControllerInterface` as dependencies. It retrieves the current HTTP request
from the provider and calls the controller with the request and the
`ExceptionMetadata`.

```php
use ExceptionHandler\Http\HttpResponseMiddleware;

$httpResponseMiddleware = new HttpResponseMiddleware($httpRequestProvider, $controller);

$exceptionHandler = new ExceptionHandler($metadataLoader, [$httpResponseMiddleware]);

$response = $exceptionHandler->handle($e); // $response is a PSR-7 MessageInterface
```

### `ControllerInterface`

The `ControllerInterface` is responsible for creating the final HTTP response.
You need to provide your own implementation of this interface.

Here's an example of a simple JSON controller (assuming you also have a PSR-17
response factory):

```php
use ExceptionHandler\Http\Controller\ControllerInterface;
use ExceptionHandler\Metadata\ExceptionMetadata;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class JsonController implements ControllerInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    public function __invoke(MessageInterface $request, ExceptionMetadata $metadata): MessageInterface
    {
        $response = $this->responseFactory->createResponse($metadata->getCode());
        $response->getBody()->write(json_encode(['message' => $metadata->getMessage()]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
```

## Translation

The package provides a `TranslationMiddleware` that allows you to translate
exception messages.

### `TranslationMiddleware`

The `TranslationMiddleware` requires a `TranslationConfigLoader` and a
`TranslatorInterface`. It loads the translation configuration for the given
exception and uses the translator to get the translated message. When no
configuration is found, the metadata is returned untouched.

```php
use ExceptionHandler\Translation\TranslationMiddleware;

$translationMiddleware = new TranslationMiddleware($configLoader, $translator);
$exceptionHandler = new ExceptionHandler($metadataLoader, [$httpResponseMiddleware, $translationMiddleware]);
```

Note the order of the middlewares: the translation middleware calls `$next($e)`
to get the metadata, so the response middleware must wrap it. Putting
`$httpResponseMiddleware` first is what makes the translated metadata reach the
controller.

### `TranslatorInterface`

You need to provide your own implementation of the `TranslatorInterface`. This
interface is compatible with the `Symfony\Contracts\Translation\TranslatorInterface`.

```php
use ExceptionHandler\Translation\TranslatorInterface;

class MyTranslator implements TranslatorInterface
{
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        // ... your translation logic
    }
}
```

### Translation Configuration

Similar to the metadata loaders, there are multiple ways to configure
translation for an exception. The `TranslationConfigLoader` can be configured
with different loaders, and the first one that supports the exception wins:

1.  **`TranslationConfigAwareTranslationConfigLoader`**: This loader works with
    exceptions that implement the `TranslationConfigAwareInterface`.

    ```php
    use ExceptionHandler\Translation\TranslationConfig;
    use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware\TranslationConfigAwareInterface;

    class MyException extends Exception implements TranslationConfigAwareInterface
    {
        public function getTranslationConfig(): TranslationConfig
        {
            return new TranslationConfig('my_exception_message');
        }
    }
    ```

2.  **`AttributeTranslationConfigLoader`**: This loader uses PHP 8 attributes to
    define the translation configuration on the exception class itself.

    ```php
    use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig;

    #[TranslationConfig(id: 'my_exception_message')]
    class MyException extends Exception
    {
    }
    ```

3.  **`StaticListTranslationConfigLoader`**: This loader uses a static list to
    map exception classes to translation configurations. See
    [Static Lists](#static-lists) for more details.

    ```php
    use ExceptionHandler\Translation\TranslationConfig;
    use ExceptionHandler\Translation\TranslationConfigLoaders\StaticListTranslationConfigLoader;

    $list = new StaticList($container);
    $list->setList([
        MyException::class => static fn (MyException $e): TranslationConfig => new TranslationConfig('my_exception_message'),
    ]);

    $configLoader = new StaticListTranslationConfigLoader($list);
    ```

You can combine multiple translation config loaders by passing them as an array
to the `TranslationConfigLoader` constructor:

```php
$configLoader = new TranslationConfigLoader([
    new TranslationConfigAwareTranslationConfigLoader(),
    new AttributeTranslationConfigLoader(),
    new StaticListTranslationConfigLoader($list),
]);
```

### Preferred Locale

`PreferredLocaleDecorator` decorates any translation config loader and applies a
preferred locale to the loaded configuration. A locale that is already set on
the `TranslationConfig` is never overwritten, and the locale falls back to `en`:

```php
use ExceptionHandler\Translation\TranslationConfigLoaders\PreferredLocaleDecorator;

$configLoader = new TranslationConfigLoader([
    new PreferredLocaleDecorator(new AttributeTranslationConfigLoader(), $preferredLocaleProvider),
]);
```

The `PreferredLocaleProviderInterface` has a single method,
`getPreferredLocale(): ?string`. A `HttpRequestAwarePreferredLocaleProvider` is
provided out of the box: it reads the `Accept-Language` header, honors its
quality values, and returns the most preferred language (ignoring the `*`
wildcard).

## License

This package is licensed under the [MIT license](LICENSE).