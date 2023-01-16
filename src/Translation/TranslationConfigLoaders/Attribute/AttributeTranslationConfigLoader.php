<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig as TranslationConfigAttribute;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use ReflectionAttribute;
use ReflectionObject;
use RuntimeException;
use Throwable;

final class AttributeTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    public function supports(Throwable $e): bool
    {
        if ($this->phpVersionIsBelow8()) {
            return false;
        }

        return $this->getTranslationConfigAttribute($e) instanceof TranslationConfigAttribute;
    }

    public function load(Throwable $e): TranslationConfig
    {
        if ($this->phpVersionIsBelow8()) {
            throw new RuntimeException(sprintf('Minimum required version for using %s is 8.0.0', __CLASS__));
        }

        $attribute = $this->getTranslationConfigAttribute($e);

        assert($attribute instanceof TranslationConfigAttribute);

        return new TranslationConfig($attribute->id, $attribute->parameters, $attribute->domain, $attribute->locale);
    }

    private function getTranslationConfigAttribute(Throwable $e): ?TranslationConfigAttribute
    {
        $refObject = new ReflectionObject($e);
        $attributes = $refObject->getAttributes(TranslationConfigAttribute::class);
        $attribute = array_shift($attributes);

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }

    /**
     * @return bool
     */
    private function phpVersionIsBelow8(): bool
    {
        return PHP_VERSION_ID < 80000;
    }
}
