@props([
    'name',
    'label' => '',
    'value' => '',
    'config' => [],
])

@php
    $editorId = Str::slug($name, '_') . '_' . uniqid();
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $editorId }}" class="form-label">{{ $label }}</label>
    @endif

    <textarea name="{{ $name }}" id="{{ $editorId }}" class="form-control">{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

@push('js')
    @push('js')
    <script src="https://cdn.ckeditor.com/4.20.0/full/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('{{ $editorId }}', {
            filebrowserBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
        });
    </script>
@endpush

@endpush
