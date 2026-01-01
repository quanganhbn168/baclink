@props([
    'name',
    'label' => '',
    'value' => '',
    'required' => false,
])

@php $rawValue = old($name, $value); @endphp

{{-- Dùng wire:ignore để JS hoạt động thoải mái mà không bị Livewire render lại --}}
<div class="form-group" wire:ignore>
    <label for="{{ $name }}_formatted">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <input
        type="text"
        id="{{ $name }}_formatted"
        class="form-control money-input-livewire"
        value="{{ $rawValue ? number_format($rawValue, 0, ',', '.') : '' }}"
        placeholder="0"
        autocomplete="off"
    >
    {{-- wire:model sẽ được truyền vào input ẩn này --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $rawValue }}"
        {{ $attributes }}
    >
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
@push('js')
<script>
    // Dùng event listener riêng cho document để tránh xung đột khi có nhiều component
    document.addEventListener('livewire:navigated', function () {
        document.querySelectorAll('.money-input-livewire').forEach(input => {
            const realInput = document.getElementById(input.id.replace('_formatted', ''));

            const updateMoney = (e) => {
                let value = e.target.value.replace(/[^\d]/g, '');
                if (value.length === 0) {
                    e.target.value = '';
                    realInput.value = '';
                } else {
                    const formatted = Number(value).toLocaleString('vi-VN');
                    e.target.value = formatted;
                    realInput.value = value;
                }
                // QUAN TRỌNG: Gửi tín hiệu "input" cho Livewire để nó biết giá trị đã thay đổi
                realInput.dispatchEvent(new Event('input'));
            };
            
            input.removeEventListener('input', updateMoney); // Xóa listener cũ để tránh lặp
            input.removeEventListener('blur', updateMoney);
            
            input.addEventListener('input', updateMoney);
            input.addEventListener('blur', updateMoney);
        });
    });
</script>
@endpush
@endonce