@props([
    'name',
    'label',
    'options' => [], // Dữ liệu ban đầu (khi edit)
    'selected' => '',
    'required' => false,
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    >
        {{-- Các option sẽ được JS render ở đây --}}
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@push('js')
<script>
document.addEventListener('livewire:navigated', () => {
    const parentSelect = document.getElementById('{{ $name }}');
    
    // Hàm render options, lấy từ code của anh
    const renderOptions = (items, grouped, prefix = '') => {
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = prefix + ' ' + item.name;
            parentSelect.appendChild(option);

            if (grouped[item.id]) {
                renderOptions(grouped[item.id], grouped, prefix + '—');
            }
        });
    };

    // Hàm cập nhật toàn bộ dropdown
    const updateCategorySelect = (categories, selectedId) => {
        parentSelect.innerHTML = '<option value="">-- Chọn danh mục --</option>';
        
        const groupedData = Object.values(categories).reduce((acc, item) => {
            const parent = item.parent_id || 0;
            if (!acc[parent]) acc[parent] = [];
            acc[parent].push(item);
            return acc;
        }, {});

        if(groupedData[0]) {
            renderOptions(groupedData[0], groupedData);
        }
        
        // Chọn lại giá trị cũ (quan trọng khi edit)
        if (selectedId) {
            parentSelect.value = selectedId;
        }
    };
    
    // Cập nhật lần đầu khi trang load (cho form edit)
    updateCategorySelect(@json($options), '{{ $selected }}');
    
    // Lắng nghe sự kiện từ Livewire và cập nhật lại
    Livewire.on('categories-updated', ({ categories }) => {
        updateCategorySelect(categories, null);
    });

    // Gửi giá trị đã chọn về cho Livewire
    parentSelect.addEventListener('change', (e) => {
        @this.set('category_id', e.target.value);
    });
});
</script>
@endpush
