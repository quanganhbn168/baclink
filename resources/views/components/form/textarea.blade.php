{{-- resources/views/components/form/textarea.blade.php --}}
@props(['name', 'label', 'value' => '', 'required' => false])

@php $inputValue = old($name, $value); @endphp

<div class="form-group">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="5"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        >{{ $inputValue }}</textarea>
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    
</div>