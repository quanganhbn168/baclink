@props([
    'name',
    'label',
    'checked' => false,
    'onText' => 'Hiện',
    'offText' => 'Ẩn',
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    
        <div class="d-flex align-items-center gap-3">
            <div class="custom-control custom-switch">
                <input type="hidden" name="{{ $name }}" value="0">
                <input
                    type="checkbox"
                    id="{{ $name }}"
                    name="{{ $name }}"
                    value="1"
                    class="custom-control-input {{ $errors->has($name) ? 'is-invalid' : '' }}"
                    {{ $checked ? 'checked' : '' }}
                    onchange="updateSwitchLabel('{{ $name }}', '{{ $onText }}', '{{ $offText }}')"
                    {{ $attributes }} 
                >
                <label class="custom-control-label" for="{{ $name }}"></label>
            </div>
            <span id="label_{{ $name }}">
                <span class="badge {{ $checked ? 'badge-success' : 'badge-secondary' }}">
                    {{ $checked ? $onText : $offText }}
                </span>
            </span>
        </div>
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    
</div>

@push('js')
<script>
    function updateSwitchLabel(name, onText, offText) {
        const input = document.getElementById(name);
        const label = document.getElementById('label_' + name);
        if (!input || !label) return;

        const isChecked = input.checked;
        label.innerHTML = `<span class="badge ${isChecked ? 'badge-success' : 'badge-secondary'}">${isChecked ? onText : offText}</span>`;
    }
</script>
@endpush
