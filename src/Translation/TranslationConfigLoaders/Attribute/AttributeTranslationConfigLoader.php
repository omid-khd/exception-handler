<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig as TranslationConfigAttribute;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use ReflectionAttribute;
use ReflectionClass;
use Throwable;
use Webmozart\Assert\Assert;

final class AttributeTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    /**
     * @var array<class-string, TranslationConfigAttribute|null>
     */
    private array $attributeCache = [];

    public function support(Throwable $e): bool
    {
        return $this->getTranslationConfigAttribute($e) !== null;
    }

    public function load(Throwable $e): TranslationConfig
    {
        $attribute = $this->getTranslationConfigAttribute($e);

        Assert::isInstanceOf($attribute, TranslationConfigAttribute::class);

        return new TranslationConfig($attribute->id, $attribute->parameters, $attribute->domain, $attribute->locale);
    }

    private function getTranslationConfigAttribute(Throwable $e): ?TranslationConfigAttribute
    {
        $class = $e::class;

        if (!array_key_exists($class, $this->attributeCache)) {
            $this->attributeCache[$class] = $this->resolveAttribute($class);
        }

        return $this->attributeCache[$class];
    }

    private function resolveAttribute(string $class): ?TranslationConfigAttribute
    {
        $attributes = (new ReflectionClass($class))->getAttributes(TranslationConfigAttribute::class);
        $attribute = $attributes[0] ?? null;

        return $attribute instanceof ReflectionAttribute ? $attribute->newInstance() : null;
    }
}
