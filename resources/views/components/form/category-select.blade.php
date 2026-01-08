@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'required' => false,
    'placeholder' => '-- Vui lòng chọn loại danh mục trước --', // Thay đổi placeholder
])

{{-- Phần PHP để build cây không thay đổi --}}
@php
    $selected = old($name, $selected);
    if (!function_exists('buildTreeOptions')) {
        function buildTreeOptions($items, $grouped, $selected, $prefix = '') {
            $result = [];
            foreach ($items as $item) {
                $result[$item->id] = $prefix . ' ' . $item->name;
                if (isset($grouped[$item->id])) {
                    $result = array_merge($result, buildTreeOptions($grouped[$item->id], $grouped, $selected, $prefix . '—'));
                }
            }
            return $result;
        }
    }
    $grouped = collect($options)->groupBy('parent_id');
    $treeOptions = buildTreeOptions($grouped[0] ?? [], $grouped, $selected);
@endphp

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        disabled
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($treeOptions as $key => $text)
            <option value="{{ $key }}" @selected($key == $selected)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('cate_type');
    const parentSelect = document.getElementById('parent_id');
    const categoryId = '{{ $category->id ?? '' }}';

    // MỚI: Kiểm tra khi tải trang, nếu đã có "Loại" thì kích hoạt "Danh mục cha"
    // Việc này quan trọng cho form edit hoặc khi có lỗi validation
    if (typeSelect.value) {
        parentSelect.disabled = false;
        // Cập nhật lại placeholder khi đã kích hoạt
        parentSelect.querySelector('option[value=""]').textContent = '-- Chọn danh mục cha --';
    }

    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;

        // Nếu không chọn gì, vô hiệu hóa và reset
        if (!selectedType) {
            parentSelect.disabled = true;
            parentSelect.innerHTML = `<option value="">-- Vui lòng chọn loại danh mục trước --</option>`;
            return;
        }

        const url = `{{ route('admin.select.categories-by-type') }}?type=${selectedType}&exclude=${categoryId}`;

        parentSelect.disabled = true;
        parentSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                parentSelect.innerHTML = `<option value="">-- Chọn danh mục cha --</option>`; // Reset
                
                function renderOptions(items, grouped, prefix = '') {
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = prefix + ' ' + item.name;
                        parentSelect.appendChild(option);

                        if (grouped[item.id]) {
                            renderOptions(grouped[item.id], grouped, prefix + '—');
                        }
                    });
                }
                
                const groupedData = data.reduce((acc, item) => {
                    const parent = item.parent_id || 0;
                    if (!acc[parent]) acc[parent] = [];
                    acc[parent].push(item);
                    return acc;
                }, {});

                if(groupedData[0]) {
                    renderOptions(groupedData[0], groupedData);
                }

                parentSelect.disabled = false;
            })
            .catch(error => {
                console.error('Lỗi khi tải danh mục:', error);
                parentSelect.innerHTML = '<option value="">Có lỗi xảy ra</option>';
            });
    });
});
</script>
@endpush
