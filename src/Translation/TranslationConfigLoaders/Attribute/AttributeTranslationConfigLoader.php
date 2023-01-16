<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig as TranslationConfigAttribute;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use ReflectionAttribute;
use ReflectionObject;
use Throwable;
use Webmozart\Assert\Assert;

final class AttributeTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    public function support(Throwable $e): bool
    {
        return $this->getTranslationConfigAttribute($e) instanceof TranslationConfigAttribute;
    }

    public function load(Throwable $e): TranslationConfig
    {
        $attribute = $this->getTranslationConfigAttribute($e);

        Assert::isInstanceOf($attribute, TranslationConfigAttribute::class);

        return new TranslationConfig($attribute->id, $attribute->parameters, $attribute->domain, $attribute->locale);
    }

    private function getTranslationConfigAttribute(Throwable $e): ?TranslationConfigAttribute
    {
        $refObject = new ReflectionObject($e);
        $attributes = $refObject->getAttributes(TranslationConfigAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) {
            return null;
        }

        $attribute = array_shift($attributes);

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }
}
