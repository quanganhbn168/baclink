{{-- resources/views/components/form/translatable-ckeditor.blade.php --}}
@props([
    'name',
    'label'    => '',
    'value'    => [],
    'required' => false,
    'config'   => [],
])

@php
    use Illuminate\Support\Str;
    $locales = config('translatable.locales', ['vi', 'en']);
    $labels  = config('translatable.labels', []);
    $translations = is_array($value) ? $value : [$locales[0] => $value];
@endphp

<div class="form-group translatable-field"
     x-data="{ activeLocale: '{{ $locales[0] }}' }">
    <label>
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
            $editorId   = Str::slug($name, '_') . '_' . $locale . '_' . uniqid();
            $fieldValue = old($name . '.' . $locale, $translations[$locale] ?? '');
        @endphp
        <div x-show="activeLocale === '{{ $locale }}'" x-cloak>
            <textarea
                name="{{ $fieldName }}"
                id="{{ $editorId }}"
                {{ $attributes->merge(['class' => 'form-control ckeditor-translatable' . ($errors->has($fieldName) ? ' is-invalid' : '')]) }}
            >{{ $fieldValue }}</textarea>
            @error($fieldName)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        @push('js')
            <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
            <script>
                (function() {
                    // Wait for tab to be visible before initializing CKEditor
                    var editorId = '{{ $editorId }}';
                    var initialized = false;

                    function initEditor() {
                        if (initialized) return;
                        var el = document.getElementById(editorId);
                        if (!el || el.offsetParent === null) return;
                        initialized = true;
                        CKEDITOR.replace(editorId, {
                            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
                            filebrowserImageBrowseUrl: '{{ asset(route('ckfinder_browser')) }}?type=Images',
                            filebrowserFlashBrowseUrl: '{{ asset(route('ckfinder_browser')) }}?type=Flash',
                            filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Files',
                            filebrowserImageUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Images',
                            filebrowserFlashUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Flash'
                        });
                    }

                    // Try init immediately (for default locale tab)
                    document.addEventListener('DOMContentLoaded', function() {
                        initEditor();
                        // Also observe for tab switches using MutationObserver
                        var container = document.getElementById(editorId);
                        if (container) {
                            var parent = container.closest('[x-show]');
                            if (parent) {
                                var observer = new MutationObserver(function() {
                                    if (parent.style.display !== 'none') {
                                        initEditor();
                                    }
                                });
                                observer.observe(parent, { attributes: true, attributeFilter: ['style'] });
                            }
                        }
                    });
                })();
            </script>
        @endpush
    @endforeach
</div>
