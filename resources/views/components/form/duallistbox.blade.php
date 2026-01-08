@props([
    'name',
    'label',
    'options' => [], // Toàn bộ các lựa chọn
    'selected' => [], // Mảng các ID đã được chọn
])

<div class="form-group">
    <label>{{ $label }}</label>
    <select multiple="multiple" name="{{ $name }}[]" class="duallistbox">
        @foreach($options as $option)
            <option value="{{ $option->id }}" @selected(in_array($option->id, $selected))>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


@push('css')
    {{-- Đường dẫn này phải trỏ đúng đến file bạn đã copy ở Bước 1.1 --}}
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
@endpush

@push('js')
    {{-- Đường dẫn này phải trỏ đúng đến file bạn đã copy ở Bước 1.1 --}}
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Khởi tạo Duallistbox
            $('.duallistbox').bootstrapDualListbox({
                nonSelectedListBoxLabel: 'Các thuộc tính có sẵn',
                selectedListBoxLabel: 'Các thuộc tính đã chọn',
                filterPlaceHolder: 'Tìm kiếm',
                infoText: 'Hiển thị tất cả {0}',
                infoTextEmpty: 'Danh sách trống',
                infoTextFiltered: '<span class="badge badge-warning">Lọc</span> {0} từ {1}',
                moveOnSelect: false, // Tắt tự động chuyển khi chọn
            });
        });
    </script>
@endpush
