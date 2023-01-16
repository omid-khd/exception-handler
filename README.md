# Exception Handler

A flexible and extensible exception handler for PHP applications.

## Installation

This package can be installed via Composer:

```bash
composer require exception-handler/exception-handler
```

**Note:** This package requires PHP 8.3 or higher.

## Usage

The core of this package is the `ExceptionHandler` class. It takes an `ExceptionMetadataLoader` and an array of middlewares in its constructor. The `handle` method is responsible for processing the exception.

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

The exception handling process is built around a middleware system. You can add custom middlewares to the `ExceptionHandler` to add custom logic to the handling process. Middlewares are callables that receive the subject (the exception in the initial call) and the next middleware in the chain.

Here's an example of a simple logging middleware:

```php
use Psr\Log\LoggerInterface;

$loggingMiddleware = function (Throwable $e, callable $next) use ($logger) {
    $logger->error($e->getMessage());

    return $next($e);
};

$exceptionHandler = new ExceptionHandler($metadataLoader, [$loggingMiddleware]);
```

The `ExceptionHandler` adds its own middleware at the end of the chain, which calls the `ExceptionMetadataLoader`. The result of the `handle` method is the result of the last middleware in the chain.

### Exception Metadata

The `ExceptionHandler` uses metadata to get information about an exception, such as the HTTP status code and a user-friendly message. The `ExceptionMetadataLoader` is responsible for loading this metadata. It iterates over a collection of `MetadataLoaderInterface` implementations until it finds one that supports the given exception.

This package provides three ways to configure exception metadata:

1.  **`StaticListMetadataLoader`**: This loader uses a static list to map exception classes to metadata factories. See the [Exception Hierarchy](#exception-hierarchy) section for more details.

    ```php
    use ExceptionHandler\Lib\StaticList;
    use ExceptionHandler\Metadata\ExceptionMetadata;
    use ExceptionHandler\Metadata\MetadataLoaders\StaticListMetadataLoader;
    
    $list = new StaticList([
        MyException::class => static fn (MyException $e): ExceptionMetadata => new ExceptionMetadata(404, 'Not Found'),
    ]);
    
    $metadataLoader = new StaticListMetadataLoader($list);
    ```

2.  **`AttributeMetadataLoader`**: This loader uses PHP 8 attributes to define metadata on the exception class itself.

    ```php
    use ExceptionHandler\Metadata\MetadataLoaders\Attribute\ThrowableMetadata;

    #[ThrowableMetadata(code: 400, message: 'Bad Request')]
    class MyException extends Exception
    {
    }
    ```

3.  **`MetadataAwareExceptionMetadataLoader`**: This loader works with exceptions that implement the `MetadataAwareExceptionInterface`.

    ```php
    use ExceptionHandler\Metadata\ExceptionMetadata;
    use ExceptionHandler\Metadata\MetadataLoaders\MetadataAware\MetadataAwareExceptionInterface;

    class MyException extends Exception implements MetadataAwareExceptionInterface
    {
        public function getMetadata(): ExceptionMetadata
        {
            return new ExceptionMetadata(403, 'Forbidden');
        }
    }
    ```

You can combine multiple metadata loaders by passing them as an array to the `ExceptionMetadataLoader` constructor.

```php
$metadataLoader = new ExceptionMetadataLoader([
    new MetadataAwareExceptionMetadataLoader(),
    new AttributeMetadataLoader(),
    new StaticListMetadataLoader($list),
]);
```

## Web Application Integration

This package is designed to be integrated into a web application. The `HttpResponseMiddleware` is a middleware that can be added to the `ExceptionHandler` to transform the `ExceptionMetadata` into an HTTP response.

### `HttpResponseMiddleware`

The `HttpResponseMiddleware` takes an `HttpRequestProviderInterface` and a `ControllerInterface` as dependencies. It retrieves the current HTTP request from the provider and calls the controller with the request and the `ExceptionMetadata`.

```php
use ExceptionHandler\Http\HttpResponseMiddleware;

$httpResponseMiddleware = new HttpResponseMiddleware($httpRequestProvider, $controller);

$exceptionHandler = new ExceptionHandler($metadataLoader, [$httpResponseMiddleware]);

$response = $exceptionHandler->handle($e); // $response will be a PSR-7 ResponseInterface
```

### `ControllerInterface`

The `ControllerInterface` is responsible for creating the final HTTP response. You need to provide your own implementation of this interface.

Here's an example of a simple JSON controller:

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

The package provides a `TranslationMiddleware` that allows you to translate exception messages.

### `TranslationMiddleware`

The `TranslationMiddleware` requires a `TranslationConfigLoader` and a `TranslatorInterface`. It loads the translation configuration for the given exception and uses the translator to get the translated message.

```php
use ExceptionHandler\Translation\TranslationMiddleware;

$translationMiddleware = new TranslationMiddleware($configLoader, $translator);
$exceptionHandler = new ExceptionHandler($metadataLoader, [$translationMiddleware]);
```

### `TranslatorInterface`

You need to provide your own implementation of the `TranslatorInterface`. This interface is compatible with the `Symfony\Contracts\Translation\TranslatorInterface`.

```php
use ExceptionHandler\Translation\TranslatorInterface;

class MyTranslator implements TranslatorInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        // ... your translation logic
    }
}
```

### Translation Configuration

Similar to the metadata loaders, there are multiple ways to configure translation for an exception. The `TranslationConfigLoader` can be configured with different loaders.

1.  **`StaticListTranslationConfigLoader`**: This loader uses a static list to map exception classes to translation configurations. See the [Exception Hierarchy](#exception-hierarchy) section for more details.

    ```php
    use ExceptionHandler\Lib\StaticList;
    use ExceptionHandler\Translation\TranslationConfig;
    use ExceptionHandler\Translation\TranslationConfigLoaders\StaticListTranslationConfigLoader;
    
    $list = new StaticList([
        MyException::class => static fn (MyException $e): TranslationConfig => new TranslationConfig('my_exception_message'),
    ]);
    
    $configLoader = new StaticListTranslationConfigLoader($list);
    ```

2.  **`AttributeTranslationConfigLoader`**: This loader uses PHP 8 attributes to define the translation configuration on the exception class itself.

    ```php
    use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig;

    #[TranslationConfig(id: 'my_exception_message')]
    class MyException extends Exception
    {
    }
    ```

3.  **`TranslationConfigAwareTranslationConfigLoader`**: This loader works with exceptions that implement the `TranslationConfigAwareInterface`.

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

You can combine multiple translation config loaders by passing them as an array to the `TranslationConfigLoader` constructor.

```php
$configLoader = new TranslationConfigLoader([
    new TranslationConfigAwareTranslationConfigLoader(),
    new AttributeTranslationConfigLoader(),
    new StaticListTranslationConfigLoader($list),
]);
```

Please refer to the source code for more details on how to configure translation.

### Exception Hierarchy

Both `StaticListMetadataLoader` and `StaticListTranslationConfigLoader` intelligently handle exception hierarchies. When looking for a configuration, they will traverse up the exception's class chain, including parent classes and implemented interfaces. This is useful for setting up metadata and translations that apply to a single exception class or to a related group of exceptions.

## License

This package is released without a license.
