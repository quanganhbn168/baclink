{{-- resources/views/components/form/translatable-input.blade.php --}}
@props([
    'name',
    'label',
    'value'       => [],
    'required'    => false,
    'type'        => 'text',
    'placeholder' => 'Thông tin',
])

@php
    $locales = config('translatable.locales', ['vi', 'en']);
    $labels  = config('translatable.labels', []);
    // Normalize value: could be array of translations or a plain string (legacy)
    $translations = is_array($value) ? $value : [$locales[0] => $value];
@endphp

<div class="form-group translatable-field"
     x-data="{ activeLocale: '{{ $locales[0] }}' }">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>

    {{-- Language tabs --}}
    <ul class="nav nav-pills nav-sm mb-2">
        @foreach($locales as $locale)
            <li class="nav-item">
                <a href="#"
                   class="nav-link py-1 px-2"
                   :class="{ 'active': activeLocale === '{{ $locale }}' }"
                   @click.prevent="activeLocale = '{{ $locale }}'">
                    {{ $labels[$locale] ?? strtoupper($locale) }}
                </a>
            </li>
        @endforeach
    </ul>

    {{-- One input per locale --}}
    @foreach($locales as $locale)
        @php
            $fieldName = $name . '[' . $locale . ']';
            $fieldValue = old($name . '.' . $locale, $translations[$locale] ?? '');
        @endphp
        <div x-show="activeLocale === '{{ $locale }}'" x-cloak>
            <input
                type="{{ $type }}"
                name="{{ $fieldName }}"
                id="{{ $name }}_{{ $locale }}"
                value="{{ $fieldValue }}"
                {{ $attributes->merge(['class' => 'form-control' . ($errors->has($fieldName) ? ' is-invalid' : '')]) }}
                placeholder="{{ $placeholder }}"
                @if($required && $locale === $locales[0]) required @endif
            >
            @error($fieldName)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
