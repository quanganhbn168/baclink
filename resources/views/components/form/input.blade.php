{{-- resources/views/components/form/input.blade.php --}}
@props(['name', 'label', 'value' => '', 'required' => false, 'type' => 'text', 'placeholder'=>'Thông tin'])

@php $inputValue = old($name, $value); @endphp

<div class="form-group">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $inputValue }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            placeholder="{{$placeholder}}"
        >
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    
</div>