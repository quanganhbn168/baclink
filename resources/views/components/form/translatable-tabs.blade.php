{{-- resources/views/components/form/translatable-tabs.blade.php --}}
@props(['activeLocale' => config('translatable.default', 'vi')])

@php
    $locales = config('translatable.locales', ['vi', 'en']);
    $labels  = config('translatable.labels', []);
@endphp

<div class="translatable-tabs" x-data="{ activeLocale: '{{ $activeLocale }}' }">
    <ul class="nav nav-pills nav-sm mb-2">
        @foreach($locales as $locale)
            <li class="nav-item">
                <a href="#"
                   class="nav-link"
                   :class="{ 'active': activeLocale === '{{ $locale }}' }"
                   @click.prevent="activeLocale = '{{ $locale }}'">
                    {{ $labels[$locale] ?? strtoupper($locale) }}
                </a>
            </li>
        @endforeach
    </ul>
    <div class="translatable-panels">
        {{ $slot }}
    </div>
</div>
