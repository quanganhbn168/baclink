@props([
    'name',
    'label' => '',
    'value' => '',
    'required' => false,
])

@php
    $rawValue = old($name, $value);
@endphp

<div class="form-group">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
        <input
            type="text"
            id="{{ $name }}_formatted"
            class="form-control money-input{{ $errors->has($name) ? ' is-invalid' : '' }}"
            value="{{ $rawValue ? number_format($rawValue, 0, ',', '.') : '' }}"
            placeholder="0"
            autocomplete="off"
        >
        <input
            type="hidden"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $rawValue }}"
        >
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
</div>

@once
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper function to format currency
            function formatCurrencyInput(input) {
                let value = input.value.replace(/[^\d]/g, '');
                let hiddenInputId = input.id.replace('_formatted', '');
                
                // Escape special characters in ID for querySelector if needed, 
                // but getElementById works fine with brackets.
                let hiddenInput = document.getElementById(hiddenInputId);

                if (value.length === 0) {
                    input.value = '';
                    if(hiddenInput) hiddenInput.value = '';
                    return;
                }

                // Format: 1.000.000
                const formatted = Number(value).toLocaleString('vi-VN');
                input.value = formatted; 
                if(hiddenInput) hiddenInput.value = value;
            }

            // Event Delegation for Input
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('money-input')) {
                    formatCurrencyInput(e.target);
                }
            });

            // Event Delegation for Blur (Optional: Enforce format)
            document.addEventListener('blur', function (e) {
                if (e.target.classList.contains('money-input')) {
                    formatCurrencyInput(e.target);
                }
            }, true); // Use capture to ensure we catch it? No, bubbling is fine for blur if focusout used, but blur doesn't bubble. Focusout does.
            
            // Use focusout for bubbling blur-like behavior
            document.addEventListener('focusout', function (e) {
                 if (e.target.classList.contains('money-input')) {
                    formatCurrencyInput(e.target);
                }
            });
        });
    </script>
    @endpush
@endonce
