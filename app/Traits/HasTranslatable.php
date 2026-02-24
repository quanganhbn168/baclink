<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations;

/**
 * Wrapper trait for Spatie's HasTranslations.
 * Models using this trait must define: public array $translatable = ['field1', 'field2'];
 * 
 * Automatically falls back to Vietnamese when a translation is missing.
 */
trait HasTranslatable
{
    use HasTranslations {
        HasTranslations::getTranslation as protected spatieGetTranslation;
    }

    /**
     * Override Spatie's getTranslation so $locale is optional.
     * This allows views to call $model->getTranslation('field') with just 1 argument.
     */
    public function getTranslation(string $key, string $locale = null, bool $useFallbackLocale = true): mixed
    {
        $locale = $locale ?: app()->getLocale();

        return $this->spatieGetTranslation($key, $locale, $useFallbackLocale);
    }

    /**
     * Fallback to Vietnamese when translation is not found for current locale.
     */
    public function getFallbackLocale(): string
    {
        return config('translatable.fallback_locale', 'vi');
    }
}
