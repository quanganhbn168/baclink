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
    use HasTranslations;

    /**
     * Fallback to Vietnamese when translation is not found for current locale.
     */
    public function getFallbackLocale(): string
    {
        return config('translatable.fallback_locale', 'vi');
    }
}
