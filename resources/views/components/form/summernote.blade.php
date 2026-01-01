@props(['name', 'label', 'value' => '', 'required' => false, 'height' => 300])

@php
    $inputValue = old($name, $value);
    $editorId = Str::slug($name, '_') . '_' . uniqid();
@endphp

<div class="form-group row">
    <label for="{{ $editorId }}" class="col-sm-2 col-form-label">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <div class="col-sm-10">
        <textarea
            name="{{ $name }}"
            id="{{ $editorId }}"
            class="form-control d-none{{ $errors->has($name) ? ' is-invalid' : '' }}"
        ></textarea>
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('js')
<script>
    $(function () {
        const content = @json($inputValue); // Giữ HTML raw
        $('#{{ $editorId }}').removeClass('d-none').summernote({
            height: {{ $height }},
        }).summernote('code', content);
    });
</script>
@endpush
