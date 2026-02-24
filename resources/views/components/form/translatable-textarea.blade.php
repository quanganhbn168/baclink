{{-- resources/views/components/form/translatable-textarea.blade.php --}}
@props([
    'name',
    'label',
    'value'    => [],
    'required' => false,
    'rows'     => 5,
])

@php
    $locales = config('translatable.locales', ['vi', 'en']);
    $labels  = config('translatable.labels', []);
    $translations = is_array($value) ? $value : [$locales[0] => $value];
@endphp

<div class="form-group translatable-field"
     x-data="{ activeLocale: '{{ $locales[0] }}' }">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>

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

    @foreach($locales as $locale)
        @php
            $fieldName  = $name . '[' . $locale . ']';
            $fieldValue = old($name . '.' . $locale, $translations[$locale] ?? '');
        @endphp
        <div x-show="activeLocale === '{{ $locale }}'" x-cloak>
            <textarea
                name="{{ $fieldName }}"
                id="{{ $name }}_{{ $locale }}"
                rows="{{ $rows }}"
                {{ $attributes->merge(['class' => 'form-control' . ($errors->has($fieldName) ? ' is-invalid' : '')]) }}
            >{{ $fieldValue }}</textarea>
            @error($fieldName)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
